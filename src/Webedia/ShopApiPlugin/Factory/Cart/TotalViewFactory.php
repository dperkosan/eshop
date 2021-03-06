<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use App\Webedia\ShopApiPlugin\View\Cart\TotalsView;

final class TotalViewFactory implements TotalViewFactoryInterface
{
    /** @var string */
    private $totalsViewClass;

    public function __construct(string $totalsViewClass)
    {
        $this->totalsViewClass = $totalsViewClass;
    }

    /** {@inheritdoc} */
    public function create(OrderInterface $cart): TotalsView
    {
        /** @var TotalsView $totalsView */
        $totalsView = new $this->totalsViewClass();

        $totalsView->promotion = $cart->getOrderPromotionTotal();
        $totalsView->total = $cart->getTotal();
        $totalsView->items = $cart->getItemsTotal();
        $totalsView->shipping = $cart->getShippingTotal();
        $totalsView->taxes = $cart->getTaxTotal();

        return $totalsView;
    }
}
