<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ConfigurableProduct extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.product.not_configurable';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy()
    {
        return 'sylius_shop_api_configurable_product_validator';
    }
}
