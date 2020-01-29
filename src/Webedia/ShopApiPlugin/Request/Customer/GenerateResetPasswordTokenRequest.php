<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Customer;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Command\Customer\GenerateResetPasswordToken;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class GenerateResetPasswordTokenRequest implements RequestInterface
{
    /** @var string */
    protected $email;

    protected function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new GenerateResetPasswordToken($this->email);
    }
}
