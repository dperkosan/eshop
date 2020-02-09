<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\View\Product;

use App\Webedia\ShopApiPlugin\View\ImageView;
use App\Webedia\ShopApiPlugin\View\PriceView;

class ProductVariantView
{
    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /** @var array */
    public $axis = [];

    /** @var array */
    public $nameAxis = [];

    /** @var array */
    public $idAxis = [];

    /** @var PriceView */
    public $price;

    /** @var PriceView|null */
    public $originalPrice;

    /** @var ImageView[] */
    public $images = [];

    public function __construct()
    {
        $this->price = new PriceView();
    }
}
