<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\PaymentInterface;
use App\Webedia\ShopApiPlugin\View\Cart\PaymentView;

interface PaymentViewFactoryInterface
{
    public function create(PaymentInterface $payment, string $locale): PaymentView;
}
