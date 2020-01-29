<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Command\Customer;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;

class SendOrderConfirmation implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    public function __construct(string $orderToken)
    {
        $this->orderToken = $orderToken;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }
}
