<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ImageInterface;
use App\Webedia\ShopApiPlugin\View\ImageView;

final class ImageViewFactory implements ImageViewFactoryInterface
{
    /** @var string */
    private $imageViewClass;

    /** @var FilterService */
    private $filterService;

    /** @var string */
    private $filter;

    public function __construct(
        string $imageViewClass,
        FilterService $filterService,
        string $filter
    ) {
        $this->imageViewClass = $imageViewClass;
        $this->filterService = $filterService;
        $this->filter = $filter;
    }

    public function create(ImageInterface $image): ImageView
    {
        /** @var ImageView $imageView */
        $imageView = new $this->imageViewClass();

        $imageView->code = $image->getType();
        $imageView->path = $image->getPath();

        $imageView->cachedPath = $this->filterService->getUrlOfFilteredImage($image->getPath(), $this->filter);

        return $imageView;
    }
}
