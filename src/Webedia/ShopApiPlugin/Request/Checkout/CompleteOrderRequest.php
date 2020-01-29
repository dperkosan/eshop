<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Checkout;

use App\Webedia\ShopApiPlugin\Command\Cart\CompleteOrder;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class CompleteOrderRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $notes;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->notes = $request->request->get('notes');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new CompleteOrder($this->token, $this->notes);
    }
}
