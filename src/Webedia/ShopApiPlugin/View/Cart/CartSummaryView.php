<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\View\Cart;

use App\Webedia\ShopApiPlugin\View\AddressBook\AddressView;
use App\Webedia\ShopApiPlugin\View\Checkout\ShipmentView;
use App\Webedia\ShopApiPlugin\View\ItemView;

class CartSummaryView
{
    /** @var string */
    public $tokenValue;

    /** @var string */
    public $channel;

    /** @var string */
    public $currency;

    /** @var string */
    public $locale;

    /** @var string */
    public $checkoutState;

    /** @var array|ItemView[] */
    public $items = [];

    /** @var TotalsView */
    public $totals;

    /** @var AddressView */
    public $shippingAddress;

    /** @var AddressView */
    public $billingAddress;

    /** @var array|PaymentView[] */
    public $payments = [];

    /** @var array|ShipmentView[] */
    public $shipments = [];

    /** @var array|AdjustmentView[] */
    public $cartDiscounts = [];

    /** @var string|null */
    public $couponCode;

    public function __construct()
    {
        $this->totals = new TotalsView();
    }
}
