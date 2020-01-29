<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Exception;

final class ChannelNotFoundException extends \InvalidArgumentException
{
    public static function occur(): self
    {
        return new self('Channel has not been found.');
    }

    public static function withCode(string $channelCode): self
    {
        return new self(sprintf('Channel with code %s has not been found.', $channelCode));
    }
}
