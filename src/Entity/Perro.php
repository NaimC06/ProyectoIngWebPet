<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Perro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $propietario = null;

    #[ORM\Column(length: 80)]
    private string $nombre = '';

    #[ORM\Column(length: 80)]
    private string $raza = '';

    #[ORM\Column]
    private int $edad = 0;

    #[ORM\Column(length: 20)]
    private string $tamano = 'mediano';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPropietario(): ?Usuario
    {
        return $this->propietario;
    }

    public function setPropietario(Usuario $propietario): static
    {
        $this->propietario = $propietario;
        return $this;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = trim($nombre);
        return $this;
    }

    public function getRaza(): string
    {
        return $this->raza;
    }

    public function setRaza(string $raza): static
    {
        $this->raza = trim($raza);
        return $this;
    }

    public function getEdad(): int
    {
        return $this->edad;
    }

    public function setEdad(int $edad): static
    {
        $this->edad = $edad;
        return $this;
    }

    public function getTamano(): string
    {
        return $this->tamano;
    }

    public function setTamano(string $tamano): static
    {
        $this->tamano = $tamano;
        return $this;
    }
}