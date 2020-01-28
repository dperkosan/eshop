<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory;

use App\Webedia\ShopApiPlugin\View\ValidationErrorView;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationErrorViewFactoryInterface
{
    public function create(ConstraintViolationListInterface $validationResults): ValidationErrorView;
}
