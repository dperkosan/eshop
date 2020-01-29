<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use App\Webedia\ShopApiPlugin\Validator\Constraints\CartExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartExistsValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /** {@inheritdoc} */
    public function validate($token, Constraint $constraint)
    {
        Assert::isInstanceOf($constraint, CartExists::class);

        if (null === $this->orderRepository->findOneBy(['tokenValue' => $token, 'state' => OrderInterface::STATE_CART])) {
            $this->context->addViolation($constraint->message);
        }
    }
}
