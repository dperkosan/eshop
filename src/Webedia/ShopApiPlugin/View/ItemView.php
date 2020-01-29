<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\View;

use App\Webedia\ShopApiPlugin\View\Product\ProductView;

class ItemView
{
    /** @var mixed */
    public $id;

    /** @var int */
    public $quantity;

    /** @var int */
    public $total;

    /** @var ProductView */
    public $product;

    public function __construct()
    {
        $this->product = new ProductView();
    }
}
