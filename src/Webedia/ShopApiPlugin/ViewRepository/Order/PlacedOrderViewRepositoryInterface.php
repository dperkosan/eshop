<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Order;

use App\Webedia\ShopApiPlugin\View\Order\PlacedOrderView;

interface PlacedOrderViewRepositoryInterface
{
    /** @return array|PlacedOrderView[] */
    public function getAllCompletedByCustomerEmail(string $customerEmail): array;

    public function getOneCompletedByCustomerEmailAndToken(string $customerEmail, string $tokenValue): PlacedOrderView;

    public function getOneCompletedByGuestAndToken(string $tokenValue): PlacedOrderView;
}
