<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private string $nombre = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $precio = '0.00';

    #[ORM\Column]
    private int $cantidadPaseos = 0;

    #[ORM\Column]
    private int $duracionDias = 0;

    #[ORM\Column(length: 20)]
    private string $estado = 'activo';

    #[ORM\Column]
    private \DateTimeImmutable $creadoEn;

    public function __construct()
    {
        $this->creadoEn = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrecio(): string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): static
    {
        $this->precio = $precio;
        return $this;
    }

    public function getCantidadPaseos(): int
    {
        return $this->cantidadPaseos;
    }

    public function setCantidadPaseos(int $cantidadPaseos): static
    {
        $this->cantidadPaseos = $cantidadPaseos;
        return $this;
    }

    public function getDuracionDias(): int
    {
        return $this->duracionDias;
    }

    public function setDuracionDias(int $duracionDias): static
    {
        $this->duracionDias = $duracionDias;
        return $this;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    public function getCreadoEn(): \DateTimeImmutable
    {
        return $this->creadoEn;
    }

    public function __toString(): string
    {
        return $this->nombre;
    }
}
