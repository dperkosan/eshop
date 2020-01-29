<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Taxon;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use App\Webedia\ShopApiPlugin\Factory\Taxon\TaxonViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use App\Webedia\ShopApiPlugin\View\Taxon\TaxonView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShowTaxonTreeAction
{
    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var TaxonViewFactoryInterface */
    private $taxonViewFactory;

    /** @var RequestBasedLocaleProviderInterface */
    private $requestBasedLocaleProvider;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ViewHandlerInterface $viewHandler,
        TaxonViewFactoryInterface $taxonViewFactory,
        RequestBasedLocaleProviderInterface $requestBasedLocaleProvider
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->viewHandler = $viewHandler;
        $this->taxonViewFactory = $taxonViewFactory;
        $this->requestBasedLocaleProvider = $requestBasedLocaleProvider;
    }

    public function __invoke(Request $request): Response
    {
        $localeCode = $this->requestBasedLocaleProvider->getLocaleCode($request);

        $taxons = $this->taxonRepository->findRootNodes();
        $taxonViews = [];

        /** @var TaxonInterface $taxon */
        foreach ($taxons as $taxon) {
            $taxonViews[] = $this->buildTaxonView($taxon, $localeCode);
        }

        return $this->viewHandler->handle(View::create($taxonViews, Response::HTTP_OK));
    }

    private function buildTaxonView(TaxonInterface $taxon, string $localeCode): TaxonView
    {
        $taxonView = $this->taxonViewFactory->create($taxon, $localeCode);

        foreach ($taxon->getChildren() as $childTaxon) {
            $taxonView->children[] = $this->buildTaxonView($childTaxon, $localeCode);
        }

        return $taxonView;
    }
}
