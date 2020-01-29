<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Command\Customer;

class EnableCustomer
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
