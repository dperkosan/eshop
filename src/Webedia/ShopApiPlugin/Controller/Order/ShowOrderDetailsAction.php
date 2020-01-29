<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Order;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use Sylius\Component\Core\Model\ShopUserInterface;
use App\Webedia\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use App\Webedia\ShopApiPlugin\View\Order\PlacedOrderView;
use App\Webedia\ShopApiPlugin\ViewRepository\Order\PlacedOrderViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowOrderDetailsAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    /** @var PlacedOrderViewRepositoryInterface */
    private $placedOrderQuery;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        LoggedInShopUserProviderInterface $loggedInUserProvider,
        PlacedOrderViewRepositoryInterface $placedOrderQuery
    ) {
        $this->viewHandler = $viewHandler;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->placedOrderQuery = $placedOrderQuery;
    }

    public function __invoke(Request $request): Response
    {
        $groups = [GroupsExclusionStrategy::DEFAULT_GROUP];
        $user = null;
        if ($this->loggedInUserProvider->isUserLoggedIn()) {
            /** @var ShopUserInterface $user */
            $user = $this->loggedInUserProvider->provide();
            $groups[] = 'logged_in_user';
        }

        try {
            $order = $this->getPlacedOrderView(
                (string) $request->attributes->get('tokenValue'),
                $user
            );
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }

        $view = View::create($order, Response::HTTP_OK);
        $view->getContext()->setGroups($groups);

        return $this->viewHandler->handle($view);
    }

    private function getPlacedOrderView(string $token, ShopUserInterface $user = null): PlacedOrderView
    {
        if (null !== $user) {
            return $this
                ->placedOrderQuery
                ->getOneCompletedByCustomerEmailAndToken($user->getEmail(), $token);
        }

        return $this
            ->placedOrderQuery
            ->getOneCompletedByGuestAndToken($token);
    }
}
