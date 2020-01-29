<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Event;

class OrderCompleted
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
