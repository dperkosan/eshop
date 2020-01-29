<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Cart;

use Ramsey\Uuid\Uuid;
use Sylius\Component\Core\Model\ChannelInterface;
use App\Webedia\ShopApiPlugin\Command\Cart\PickupCart;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class PickupCartRequest implements ChannelBasedRequestInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $channelCode;

    protected function __construct(string $channelCode)
    {
        $this->token = Uuid::uuid4()->toString();
        $this->channelCode = $channelCode;
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new PickupCart($this->token, $this->channelCode);
    }
}
