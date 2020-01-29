<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Cart;

use App\Webedia\ShopApiPlugin\View\Cart\CartSummaryView;

interface CartViewRepositoryInterface
{
    public function getOneByToken(string $token): CartSummaryView;
}
