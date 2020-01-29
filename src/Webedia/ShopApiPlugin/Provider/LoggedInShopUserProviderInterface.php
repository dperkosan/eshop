<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Provider;

use Sylius\Component\Core\Model\ShopUserInterface;

interface LoggedInShopUserProviderInterface
{
    public function provide(): ShopUserInterface;

    public function isUserLoggedIn(): bool;
}
