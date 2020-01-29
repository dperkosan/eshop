<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ConfigurableProductValidator extends ConstraintValidator
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /** {@inheritdoc} */
    public function validate($productCode, Constraint $constraint)
    {
        if (null === $productCode) {
            return;
        }

        $product = $this->productRepository->findOneByCode($productCode);

        if (null === $product || $product->isConfigurable()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
