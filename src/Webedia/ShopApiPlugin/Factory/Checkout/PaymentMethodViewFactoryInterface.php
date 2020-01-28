<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use App\Webedia\ShopApiPlugin\View\Cart\PaymentMethodView;

interface PaymentMethodViewFactoryInterface
{
    public function create(PaymentMethodInterface $paymentMethod, string $locale): PaymentMethodView;
}
