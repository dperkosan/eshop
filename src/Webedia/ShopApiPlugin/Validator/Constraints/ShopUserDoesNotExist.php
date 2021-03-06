<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ShopUserDoesNotExist extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.email.unique';

    /** {@inheritdoc} */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return 'sylius_shop_api_shop_user_does_not_exist_validator';
    }
}
