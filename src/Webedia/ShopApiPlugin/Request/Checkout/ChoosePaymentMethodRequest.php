<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Checkout;

use App\Webedia\ShopApiPlugin\Command\Cart\ChoosePaymentMethod;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ChoosePaymentMethodRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $paymentId;

    /** @var string|null */
    protected $method;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->paymentId = $request->attributes->get('paymentId');
        $this->method = $request->request->get('method');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new ChoosePaymentMethod($this->token, $this->paymentId, $this->method);
    }
}
