<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Provider;

use Sylius\Component\Core\Model\ProductReviewerInterface;

final class ProductReviewerProvider implements ProductReviewerProviderInterface
{
    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(CustomerProviderInterface $customerProvider)
    {
        $this->customerProvider = $customerProvider;
    }

    public function provide(string $email): ProductReviewerInterface
    {
        return $this->customerProvider->provide($email);
    }
}
