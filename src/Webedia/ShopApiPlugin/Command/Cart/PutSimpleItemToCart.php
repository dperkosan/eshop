<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Command\Cart;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use Webmozart\Assert\Assert;

class PutSimpleItemToCart implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var string */
    protected $product;

    /** @var int */
    protected $quantity;

    public function __construct(string $orderToken, string $product, int $quantity)
    {
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function product(): string
    {
        return $this->product;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
