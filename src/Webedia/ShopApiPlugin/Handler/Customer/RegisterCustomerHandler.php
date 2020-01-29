<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Handler\Customer;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use App\Webedia\ShopApiPlugin\Command\Customer\RegisterCustomer;
use App\Webedia\ShopApiPlugin\Event\CustomerRegistered;
use App\Webedia\ShopApiPlugin\Provider\CustomerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

final class RegisterCustomerHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var FactoryInterface */
    private $userFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ChannelRepositoryInterface $channelRepository,
        FactoryInterface $userFactory,
        EventDispatcherInterface $eventDispatcher,
        CustomerProviderInterface $customerProvider
    ) {
        $this->userRepository = $userRepository;
        $this->channelRepository = $channelRepository;
        $this->userFactory = $userFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->customerProvider = $customerProvider;
    }

    public function __invoke(RegisterCustomer $command): void
    {
        $this->assertEmailIsNotTaken($command->email());
        $this->assertChannelExists($command->channelCode());

        $customer = $this->customerProvider->provide($command->email());

        $customer->setFirstName($command->firstName());
        $customer->setLastName($command->lastName());
        $customer->setEmail($command->email());

        /** @var ShopUserInterface $user */
        $user = $this->userFactory->createNew();
        $user->setPlainPassword($command->plainPassword());
        $user->setUsername($command->email());
        $user->setCustomer($customer);

        $this->userRepository->add($user);

        $this->eventDispatcher->dispatch('sylius.customer.post_api_registered', new CustomerRegistered(
            $command->email(),
            $command->firstName(),
            $command->lastName(),
            $command->channelCode()
        ));
    }

    private function assertEmailIsNotTaken(string $email): void
    {
        Assert::null($this->userRepository->findOneByEmail($email), 'User with given email already exists.');
    }

    private function assertChannelExists(string $channelCode): void
    {
        Assert::notNull($this->channelRepository->findOneByCode($channelCode), 'Channel does not exist.');
    }
}
