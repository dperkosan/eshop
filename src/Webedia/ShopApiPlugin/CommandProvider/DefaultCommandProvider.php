<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\CommandProvider;

use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

final class DefaultCommandProvider implements CommandProviderInterface
{
    /** @var string */
    private $requestClass;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(string $requestClass, ValidatorInterface $validator)
    {
        Assert::implementsInterface($requestClass, RequestInterface::class);

        $this->requestClass = $requestClass;
        $this->validator = $validator;
    }

    public function validate(Request $httpRequest, array $constraints = null, array $groups = null): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformHttpRequest($httpRequest), $constraints, $groups);
    }

    public function getCommand(Request $httpRequest): CommandInterface
    {
        return $this->transformHttpRequest($httpRequest)->getCommand();
    }

    private function transformHttpRequest(Request $httpRequest): RequestInterface
    {
        /** @var RequestInterface $request */
        $request = $this->requestClass::fromHttpRequest($httpRequest);

        return $request;
    }
}
