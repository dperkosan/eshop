<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\PaymentInterface;
use App\Webedia\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use App\Webedia\ShopApiPlugin\View\Cart\PaymentView;
use Webmozart\Assert\Assert;

final class PaymentViewFactory implements PaymentViewFactoryInterface
{
    /** @var PaymentMethodViewFactoryInterface */
    private $paymentMethodViewFactory;

    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var string */
    private $paymentViewClass;

    public function __construct(
        PaymentMethodViewFactoryInterface $paymentMethodViewFactory,
        PriceViewFactoryInterface $priceViewFactory,
        string $paymentViewClass
    ) {
        $this->paymentMethodViewFactory = $paymentMethodViewFactory;
        $this->priceViewFactory = $priceViewFactory;
        $this->paymentViewClass = $paymentViewClass;
    }

    /** {@inheritdoc} */
    public function create(PaymentInterface $payment, string $locale): PaymentView
    {
        /** @var PaymentView $paymentView */
        $paymentView = new $this->paymentViewClass();

        $paymentView->state = $payment->getState();
        $paymentMethod = $payment->getMethod();
        Assert::notNull($paymentMethod);

        $paymentView->method = $this->paymentMethodViewFactory->create($paymentMethod, $locale);
        $paymentView->price = $this->priceViewFactory->create($payment->getAmount(), $payment->getCurrencyCode());

        return $paymentView;
    }
}
