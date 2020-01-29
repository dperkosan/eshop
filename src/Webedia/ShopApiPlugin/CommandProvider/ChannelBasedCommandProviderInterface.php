<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\CommandProvider;

use Sylius\Component\Core\Model\ChannelInterface;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ChannelBasedCommandProviderInterface
{
    public function validate(
        Request $httpRequest,
        ChannelInterface $channel,
        array $constraints = null,
        array $groups = null
    ): ConstraintViolationListInterface;

    public function getCommand(Request $httpRequest, ChannelInterface $channel): CommandInterface;
}
