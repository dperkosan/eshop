<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use App\Webedia\ShopApiPlugin\View\Cart\PaymentMethodView;

final class PaymentMethodViewFactory implements PaymentMethodViewFactoryInterface
{
    /** @var string */
    private $paymentMethodViewClass;

    public function __construct(string $paymentMethodViewClass)
    {
        $this->paymentMethodViewClass = $paymentMethodViewClass;
    }

    /** {@inheritdoc} */
    public function create(PaymentMethodInterface $paymentMethod, string $locale): PaymentMethodView
    {
        /** @var PaymentMethodView $paymentMethodView */
        $paymentMethodView = new $this->paymentMethodViewClass();

        $translation = $paymentMethod->getTranslation($locale);

        $paymentMethodView->code = $paymentMethod->getCode() ?? '';
        $paymentMethodView->name = $translation->getName() ?? '';
        $paymentMethodView->description = $translation->getDescription() ?? '';
        $paymentMethodView->instructions = $translation->getInstructions() ?? '';

        return $paymentMethodView;
    }
}
