<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\AddressBook;

use Sylius\Component\Core\Model\AddressInterface;
use App\Webedia\ShopApiPlugin\View\AddressBook\AddressView;

interface AddressViewFactoryInterface
{
    public function create(AddressInterface $address): AddressView;
}
