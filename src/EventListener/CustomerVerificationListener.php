<?php declare(strict_types=1);

namespace App\EventListener;

use App\Event\CustomerPhoneVerification;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CustomerVerificationListener
{
    /** @var MessageBusInterface */
    private $bus;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(MessageBusInterface $bus, ChannelRepositoryInterface $channelRepository)
    {
        $this->bus = $bus;
        $this->channelRepository = $channelRepository;
    }

    public function onCustomerPhoneVerification(CustomerPhoneVerification $event)
    {
        $customer = $event->getCustomer();
        // send password
        $password = $customer->getUser()->getPlainPassword();
        $phone = $customer->getPhoneNumber();
        
        // TODO implement message
        // $this->bus->dispatch(new SendVerificationToken($phone, $password));
    }
}
