<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Mapper;

use Sylius\Component\Core\Model\AddressInterface;
use App\Webedia\ShopApiPlugin\Model\Address;

interface AddressMapperInterface
{
    public function map(Address $addressData): AddressInterface;

    public function mapExisting(AddressInterface $address, Address $addressData): AddressInterface;
}
