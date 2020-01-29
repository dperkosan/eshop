<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Customer;

use Sylius\Component\Core\Model\ChannelInterface;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Command\Customer\RegisterCustomer;
use App\Webedia\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterCustomerRequest implements ChannelBasedRequestInterface
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $plainPassword;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $channelCode;

    protected function __construct(Request $request, string $channelCode)
    {
        $this->channelCode = $channelCode;

        $this->email = $request->request->get('email');
        $this->plainPassword = $request->request->get('plainPassword');
        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($request, $channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new RegisterCustomer(
            $this->email,
            $this->plainPassword,
            $this->firstName,
            $this->lastName,
            $this->channelCode
        );
    }
}
