<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Command\Customer;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;

class SendVerificationToken implements CommandInterface
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $channelCode;

    public function __construct(string $email, string $channelCode)
    {
        $this->email = $email;
        $this->channelCode = $channelCode;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }
}
