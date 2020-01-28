<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Http;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use App\Webedia\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class RequestBasedLocaleProvider implements RequestBasedLocaleProviderInterface
{
    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        ChannelContextInterface $channelContext,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->channelContext = $channelContext;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    /** @throws ChannelNotFoundException */
    public function getLocaleCode(Request $request): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return $this->supportedLocaleProvider->provide($request->query->get('locale'), $channel);
    }
}
