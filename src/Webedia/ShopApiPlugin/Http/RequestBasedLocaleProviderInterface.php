<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Http;

use App\Webedia\ShopApiPlugin\Exception\ChannelNotFoundException;
use Symfony\Component\HttpFoundation\Request;

interface RequestBasedLocaleProviderInterface
{
    /** @throws ChannelNotFoundException|\InvalidArgumentException */
    public function getLocaleCode(Request $request): string;
}
