<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\View\Cart;

use App\Webedia\ShopApiPlugin\View\PriceView;

class PaymentView
{
    /** @var string */
    public $state;

    /** @var PaymentMethodView */
    public $method;

    /** @var PriceView */
    public $price;

    public function __construct()
    {
        $this->method = new PaymentMethodView();
        $this->price = new PriceView();
    }
}
