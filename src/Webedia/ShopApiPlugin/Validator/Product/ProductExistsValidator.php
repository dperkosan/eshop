<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ProductExistsValidator extends ConstraintValidator
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
        if (null === $productCode || null === $this->productRepository->findOneByCode($productCode)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
