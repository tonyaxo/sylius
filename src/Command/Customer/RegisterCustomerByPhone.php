<?php

declare(strict_types=1);

namespace App\Command\Customer;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class RegisterCustomerByPhone implements CommandInterface
{
    /** @var string */
    protected $phoneNumber;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $channelCode;

    public function __construct(
        string $phoneNumber, 
        string $firstName, 
        string $channelCode
    ) {
        $this->phoneNumber = $phoneNumber;
        $this->firstName = $firstName;
        $this->channelCode = $channelCode;
    }

    /**
     * Get the value of channelCode
     */ 
    public function getChannelCode()
    {
        return $this->channelCode;
    }

    /**
     * Set the value of channelCode
     *
     * @return  self
     */ 
    public function setChannelCode($channelCode)
    {
        $this->channelCode = $channelCode;

        return $this;
    }

    /**
     * Get the value of firstName
     */ 
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */ 
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of phoneNumber
     */ 
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the value of phoneNumber
     *
     * @return  self
     */ 
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}
