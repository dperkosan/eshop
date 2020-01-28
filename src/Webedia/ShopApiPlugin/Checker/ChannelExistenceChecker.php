<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Checker;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use App\Webedia\ShopApiPlugin\Exception\ChannelNotFoundException;

final class ChannelExistenceChecker implements ChannelExistenceCheckerInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function withCode(string $channelCode): void
    {
        $channel = $this->channelRepository->findOneByCode($channelCode);

        if (null === $channel) {
            throw ChannelNotFoundException::withCode($channelCode);
        }
    }
}
