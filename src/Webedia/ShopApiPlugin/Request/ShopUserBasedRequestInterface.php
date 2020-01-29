<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request;

use Sylius\Component\Core\Model\ShopUserInterface;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;

interface ShopUserBasedRequestInterface
{
    public static function fromHttpRequestAndShopUser(Request $request, ShopUserInterface $user): self;

    public function getCommand(): CommandInterface;
}
