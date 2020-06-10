<?php declare(strict_types=1);

namespace App\Handler\Customer;

use App\Command\Customer\RegisterCustomerByPhone;
use App\Event\CustomerPhoneVerification;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

final class RegisterCustomerByPhoneHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var FactoryInterface */
    private $userFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ChannelRepositoryInterface $channelRepository,
        FactoryInterface $userFactory,
        EventDispatcherInterface $eventDispatcher,
        CustomerProviderInterface $customerProvider
    ) {
        $this->userRepository = $userRepository;
        $this->channelRepository = $channelRepository;
        $this->userFactory = $userFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->customerProvider = $customerProvider;
    }

    public function __invoke(RegisterCustomerByPhone $command): void
    {
        $this->assertChannelExists($command->getChannelCode());

        $username = $command->getPhoneNumber();
        $customer = $this->customerProvider->provide($username);

        $user = $customer->getUser();
        
        // Create user for new customer.
        if ($user === null) {
            $customer->setFirstName($command->getFirstName());

            /** @var ShopUserInterface $user */
            $user = $this->userFactory->createNew();
            // $user->setUsername($username); it keeps up-to-date by DefaultUsernameORMListener
            $user->setCustomer($customer);
            // sylius.customer.post_api_registered
        }

        $user->setVerifiedAt(new \DateTime());
        $user->setEmailVerificationToken(null);
        $user->setEnabled(true);

        // PRE_PASSWORD_RESET
        $password = rand(1000, 9999);
        $user->setPlainPassword("$password");    // send by SMS
        $this->userRepository->add($user);
        // POST_PASSWORD_RESET

        $event = new CustomerPhoneVerification($customer, $command->getChannelCode());
        $this->eventDispatcher->dispatch($event, CustomerPhoneVerification::EVENT_NAME);
    }

    private function assertChannelExists(string $channelCode): void
    {
        Assert::notNull($this->channelRepository->findOneByCode($channelCode), 'Channel does not exist.');
    }
}
