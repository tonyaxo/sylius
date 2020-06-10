<?php declare(strict_types=1);
namespace App\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CustomerPhoneVerification extends Event
{
    public const EVENT_NAME = 'app.customer.phone_verification';

    private $customer;

    /** @var string */
    private $channelCode;

    public function __construct(CustomerInterface $customer, string $channelCode)
    {
        $this->customer = $customer;
        $this->channelCode = $channelCode;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }

    /**
     * Get the value of customer
     */ 
    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }
}
