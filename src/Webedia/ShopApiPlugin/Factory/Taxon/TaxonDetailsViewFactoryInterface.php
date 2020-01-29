<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Taxon;

use Sylius\Component\Core\Model\TaxonInterface;
use App\Webedia\ShopApiPlugin\View\Taxon\TaxonDetailsView;

interface TaxonDetailsViewFactoryInterface
{
    public function create(TaxonInterface $taxon, string $localeCode): TaxonDetailsView;
}
