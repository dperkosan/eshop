<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\CommandProvider;

use Sylius\Component\Core\Model\ChannelInterface;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

final class ChannelBasedCommandProvider implements ChannelBasedCommandProviderInterface
{
    /** @var string */
    private $requestClass;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(string $requestClass, ValidatorInterface $validator)
    {
        Assert::implementsInterface($requestClass, ChannelBasedRequestInterface::class);

        $this->requestClass = $requestClass;
        $this->validator = $validator;
    }

    public function validate(
        Request $httpRequest,
        ChannelInterface $channel,
        array $constraints = null,
        array $groups = null
    ): ConstraintViolationListInterface {
        return $this->validator->validate($this->transformHttpRequest($httpRequest, $channel), $constraints, $groups);
    }

    public function getCommand(Request $httpRequest, ChannelInterface $channel): CommandInterface
    {
        return $this->transformHttpRequest($httpRequest, $channel)->getCommand();
    }

    private function transformHttpRequest(Request $httpRequest, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        /** @var ChannelBasedRequestInterface $request */
        $request = $this->requestClass::fromHttpRequestAndChannel($httpRequest, $channel);

        return $request;
    }
}
