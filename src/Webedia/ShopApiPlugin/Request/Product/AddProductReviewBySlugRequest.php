<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Request\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;
use App\Webedia\ShopApiPlugin\Command\Product\AddProductReviewBySlug;
use App\Webedia\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class AddProductReviewBySlugRequest implements ChannelBasedRequestInterface
{
    /** @var string */
    protected $slug;

    /** @var string */
    protected $channelCode;

    /** @var string */
    protected $title;

    /** @var int */
    protected $rating;

    /** @var string */
    protected $comment;

    /** @var string */
    protected $email;

    protected function __construct(Request $request, string $channelCode)
    {
        $this->slug = $request->attributes->get('slug');
        $this->title = $request->request->get('title');
        $this->rating = $request->request->get('rating');
        $this->comment = $request->request->get('comment');
        $this->email = $request->request->get('email');

        $this->channelCode = $channelCode;
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($request, $channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new AddProductReviewBySlug(
            $this->slug,
            $this->channelCode,
            $this->title,
            $this->rating,
            $this->comment,
            $this->email
        );
    }
}
