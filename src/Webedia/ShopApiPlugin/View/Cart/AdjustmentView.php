<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\View\Cart;

use App\Webedia\ShopApiPlugin\View\PriceView;

class AdjustmentView
{
    /** @var string */
    public $name;

    /** @var PriceView */
    public $amount;

    public function __construct()
    {
        $this->amount = new PriceView();
    }
}
