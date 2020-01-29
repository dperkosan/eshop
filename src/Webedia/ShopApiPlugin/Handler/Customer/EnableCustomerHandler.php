<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Handler\Customer;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use App\Webedia\ShopApiPlugin\Command\Customer\EnableCustomer;
use Webmozart\Assert\Assert;

final class EnableCustomerHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(EnableCustomer $event): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneByEmail($event->email());

        Assert::notNull($user);

        $user->enable();
    }
}
