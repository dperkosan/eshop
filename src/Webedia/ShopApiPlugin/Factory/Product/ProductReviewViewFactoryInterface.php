<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ProductReview;
use App\Webedia\ShopApiPlugin\View\Product\ProductReviewView;

interface ProductReviewViewFactoryInterface
{
    public function create(ProductReview $productReview): ProductReviewView;
}
