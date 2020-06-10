<?php declare(strict_types=1);

namespace App\Provider;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Exception\WrongUserException;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;

/**
 * Customer provider that use phone number.
 */
final class ShopUserAwareCustomerProvider implements CustomerProviderInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInShopUserProvider;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->loggedInShopUserProvider = $loggedInShopUserProvider;
    }

    public function provide(string $phoneNumber): CustomerInterface
    {
        if ($this->loggedInShopUserProvider->isUserLoggedIn()) {
            $loggedInUser = $this->loggedInShopUserProvider->provide();

            /** @var CustomerInterface $customer */
            $customer = $loggedInUser->getCustomer();

            if ($customer->getPhoneNumber() !== $phoneNumber) {
                throw new WrongUserException('Cannot finish checkout for other user, if customer is logged in.');
            }

            return $customer;
        }

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['phoneNumber' => $phoneNumber]);

        if ($customer === null) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setPhoneNumber($phoneNumber);

            $this->customerRepository->add($customer);

            return $customer;
        }

        // TODO remove this part
        // if ($customer->getUser() !== null) {
        //     throw new WrongUserException('Customer already registered.');
        // }

        return $customer;
    }
}
