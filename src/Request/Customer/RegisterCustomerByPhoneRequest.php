<?php


declare(strict_types=1);

namespace App\Request\Customer;

use App\Command\Customer\RegisterCustomerByPhone;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterCustomerByPhoneRequest implements ChannelBasedRequestInterface
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    protected $phoneNumber;

    protected function __construct(Request $request, string $channelCode)
    {
        $this->channelCode = $channelCode;

        // TODO https://github.com/odolbeau/phone-number-bundle
        $this->phoneNumber = $request->request->get('phoneNumber');
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($request, $channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new RegisterCustomerByPhone(
            $this->phoneNumber,
            $this->phoneNumber, // firstName as phone
            $this->channelCode
        );
    }
}
