<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Cart;

use App\Webedia\ShopApiPlugin\Command\Cart\AddCoupon;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class AddCouponRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $coupon;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->coupon = $request->request->get('coupon');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new AddCoupon($this->token, $this->coupon);
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getCoupon(): ?string
    {
        return $this->coupon;
    }
}
