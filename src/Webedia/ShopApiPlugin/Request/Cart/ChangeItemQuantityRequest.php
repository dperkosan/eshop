<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Cart;

use App\Webedia\ShopApiPlugin\Command\Cart\ChangeItemQuantity;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ChangeItemQuantityRequest implements RequestInterface
{
    /** @var string */
    protected $token;

    /** @var mixed */
    protected $id;

    /** @var int */
    protected $quantity;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->id = $request->attributes->get('id');
        $this->quantity = $request->request->getInt('quantity');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new ChangeItemQuantity($this->token, $this->id, $this->quantity);
    }
}
