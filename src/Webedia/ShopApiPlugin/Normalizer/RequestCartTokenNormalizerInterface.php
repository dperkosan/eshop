<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Normalizer;

use Symfony\Component\HttpFoundation\Request;

interface RequestCartTokenNormalizerInterface
{
    public function doNotAllowNullCartToken(Request $request): Request;
}
