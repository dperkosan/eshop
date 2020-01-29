<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Cart;

use App\Webedia\ShopApiPlugin\Command\Cart\RemoveItemFromCart;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class RemoveItemFromCartRequest implements RequestInterface
{
    /** @var string */
    protected $token;

    /** @var mixed */
    protected $id;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->id = $request->attributes->get('id');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new RemoveItemFromCart($this->token, $this->id);
    }
}
