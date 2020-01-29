<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Command\Customer;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;

class VerifyAccount implements CommandInterface
{
    /** @var string */
    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function token(): string
    {
        return $this->token;
    }
}
