<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use App\Webedia\ShopApiPlugin\View\ImageView;

interface ImageViewFactoryInterface
{
    public function create(ImageInterface $image): ImageView;
}
