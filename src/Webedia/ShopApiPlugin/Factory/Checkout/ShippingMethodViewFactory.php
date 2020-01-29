<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use App\Webedia\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use App\Webedia\ShopApiPlugin\View\Cart\ShippingMethodView;
use Webmozart\Assert\Assert;

final class ShippingMethodViewFactory implements ShippingMethodViewFactoryInterface
{
    /** @var ServiceRegistry */
    private $calculatorRegistry;

    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var string */
    private $shippingMethodViewClass;

    public function __construct(
        ServiceRegistry $calculatorRegistry,
        PriceViewFactoryInterface $priceViewFactory,
        string $shippingMethodViewClass
    ) {
        $this->calculatorRegistry = $calculatorRegistry;
        $this->priceViewFactory = $priceViewFactory;
        $this->shippingMethodViewClass = $shippingMethodViewClass;
    }

    /** {@inheritdoc} */
    public function create(ShipmentInterface $shipment, string $locale, string $currency): ShippingMethodView
    {
        $shippingMethod = $shipment->getMethod();
        Assert::notNull($shippingMethod);

        return $this->createWithShippingMethod($shipment, $shippingMethod, $locale, $currency);
    }

    /** {@inheritdoc} */
    public function createWithShippingMethod(
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        string $locale,
        string $currency
    ): ShippingMethodView {
        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculatorRegistry->get($shippingMethod->getCalculator());

        /** @var ShippingMethodView $shippingMethodView */
        $shippingMethodView = new $this->shippingMethodViewClass();

        $shippingMethodView->code = $shippingMethod->getCode();
        $shippingMethodView->name = $shippingMethod->getTranslation($locale)->getName();
        $shippingMethodView->description = $shippingMethod->getTranslation($locale)->getDescription();
        $shippingMethodView->price = $this->priceViewFactory->create(
            $calculator->calculate($shipment, $shippingMethod->getConfiguration()),
            $currency
        );

        return $shippingMethodView;
    }
}
