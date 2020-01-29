<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Cart;

use App\Webedia\ShopApiPlugin\Command\Cart\RemoveCoupon;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class RemoveCouponRequest implements RequestInterface
{
    /** @var string */
    protected $token;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new RemoveCoupon($this->token);
    }
}
