<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Command\Cart;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;

class RemoveCoupon implements CommandInterface
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
