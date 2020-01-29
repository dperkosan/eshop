<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Command\Customer;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;

class GenerateResetPasswordToken implements CommandInterface
{
    /** @var string */
    protected $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function email(): string
    {
        return $this->email;
    }
}
