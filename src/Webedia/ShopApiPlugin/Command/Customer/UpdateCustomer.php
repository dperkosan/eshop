<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Command\Customer;

use DateTimeImmutable;
use App\Webedia\ShopApiPlugin\Command\CommandInterface;

class UpdateCustomer implements CommandInterface
{
    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $email;

    /** @var DateTimeImmutable|null */
    protected $birthday;

    /** @var string */
    protected $gender;

    /** @var string|null */
    protected $phoneNumber;

    /** @var bool */
    protected $subscribedToNewsletter;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        ?DateTimeImmutable $birthday,
        string $gender,
        ?string $phoneNumber,
        ?bool $subscribedToNewsletter
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->birthday = $birthday;
        $this->gender = $gender;
        $this->phoneNumber = $phoneNumber;
        $this->subscribedToNewsletter = $subscribedToNewsletter ?? false;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function birthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function gender(): ?string
    {
        return $this->gender;
    }

    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function subscribedToNewsletter(): bool
    {
        return $this->subscribedToNewsletter;
    }
}
