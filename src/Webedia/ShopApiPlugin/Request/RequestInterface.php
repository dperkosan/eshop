<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestInterface
{
    public static function fromHttpRequest(Request $request): self;

    public function getCommand(): CommandInterface;
}
