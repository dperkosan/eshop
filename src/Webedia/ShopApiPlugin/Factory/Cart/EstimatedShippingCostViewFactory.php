<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Cart;

use App\Webedia\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Shipping\ShippingCost;
use App\Webedia\ShopApiPlugin\View\Cart\EstimatedShippingCostView;

final class EstimatedShippingCostViewFactory implements EstimatedShippingCostViewFactoryInterface
{
    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var string */
    private $className;

    public function __construct(
        PriceViewFactoryInterface $priceViewFactory,
        string $className
    ) {
        $this->priceViewFactory = $priceViewFactory;
        $this->className = $className;
    }

    public function create(ShippingCost $shippingCost): EstimatedShippingCostView
    {
        $estimatedShippingCostView = new $this->className();
        $estimatedShippingCostView->price = $this->priceViewFactory->create(
            $shippingCost->price(), $shippingCost->currency()
        );

        return $estimatedShippingCostView;
    }
}
