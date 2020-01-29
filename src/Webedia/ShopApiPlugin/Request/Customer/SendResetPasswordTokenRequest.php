<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Customer;

use Sylius\Component\Core\Model\ChannelInterface;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Command\Customer\SendResetPasswordToken;
use App\Webedia\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class SendResetPasswordTokenRequest implements ChannelBasedRequestInterface
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $channelCode;

    protected function __construct(Request $request, string $channelCode)
    {
        $this->email = $request->request->get('email');
        $this->channelCode = $channelCode;
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($request, $channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new SendResetPasswordToken($this->email, $this->channelCode);
    }
}
