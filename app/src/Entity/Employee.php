<?php

namespace App\Entity;

use DateTimeInterface;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Employee
{

    private ?Gender $gender = null;

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('name', new Length(min: 3, max: 250));

        $metadata->addPropertyConstraint('birthDate', new NotBlank());
        $metadata->addPropertyConstraint(
            'birthDate',
            new Type(\DateTimeInterface::class)
        );
    }

    private ?string $id = null;
    private ?string $name = null;
    private ?DateTimeInterface $birthDate = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function getBirthDate(): DateTimeInterface
    {
        return $this->birthDate;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setName(string $name): void
    {
        if (strlen($name) > 250 || strlen($name) < 3) {
            throw new InvalidArgumentException("Employee name has to be between 3 and 250 characters long.");
        }
        $this->name = $name;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setBirthDate(DateTimeInterface $birthDate): void
    {
        if ($birthDate->getTimestamp() > time()) {
            throw new InvalidArgumentException("Date of birth cannot be in the future.");
        }
        $this->birthDate = $birthDate;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setGender(Gender $gender): void
    {
        $this->gender = $gender;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

}
