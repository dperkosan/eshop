<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\View;

class ValidationErrorView
{
    /** @var int */
    public $code;

    /** @var string */
    public $message;

    /** @var array */
    public $errors = [];
}
