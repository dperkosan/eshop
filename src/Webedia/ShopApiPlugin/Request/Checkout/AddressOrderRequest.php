<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Checkout;

use App\Webedia\ShopApiPlugin\Command\Cart\AddressOrder;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Model\Address;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class AddressOrderRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var array|null */
    protected $shippingAddress;

    /** @var array|null */
    protected $billingAddress;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->shippingAddress = $request->request->get('shippingAddress');
        $this->billingAddress = $request->request->get('billingAddress') ?: $request->request->get('shippingAddress');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new AddressOrder(
            $this->token,
            Address::createFromArray($this->shippingAddress),
            Address::createFromArray($this->billingAddress)
        );
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getShippingAddress(): ?array
    {
        return $this->shippingAddress;
    }

    public function getBillingAddress(): ?array
    {
        return $this->billingAddress;
    }
}
