<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "L'adresse doit faire au moins {{ limit }} caractères",
        maxMessage: "L'adresse ne peut pas faire plus de {{ limit }} caractères"
    )]
    private ?string $address = null;

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire")]
    #[Assert\Regex(
        pattern: '/^[0-9]{5}$/',
        message: 'Le code postal doit contenir exactement 5 chiffres'
    )]
    private ?string $code_postal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La ville est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom de la ville doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom de la ville ne peut pas faire plus de {{ limit }} caractères"
    )]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le pays est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom du pays doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom du pays ne peut pas faire plus de {{ limit }} caractères"
    )]
    private ?string $country = null;

    #[ORM\OneToOne(inversedBy: 'address')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): static
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
