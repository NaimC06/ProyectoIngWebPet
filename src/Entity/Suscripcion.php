<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Suscripcion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: Plan::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plan $plan = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeInterface $fechaInicio = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeInterface $fechaFin = null;

    #[ORM\Column]
    private int $paseosIncluidos = 0;

    #[ORM\Column]
    private int $paseosUtilizados = 0;

    #[ORM\Column(length: 20)]
    private string $estado = 'activa';

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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(Plan $plan): static
    {
        $this->plan = $plan;
        $this->paseosIncluidos = $plan->getCantidadPaseos();
        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): static
    {
        $this->fechaInicio = $fechaInicio;
        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(\DateTimeInterface $fechaFin): static
    {
        $this->fechaFin = $fechaFin;
        return $this;
    }

    public function getPaseosIncluidos(): int
    {
        return $this->paseosIncluidos;
    }

    public function setPaseosIncluidos(int $paseosIncluidos): static
    {
        $this->paseosIncluidos = $paseosIncluidos;
        return $this;
    }

    public function getPaseosUtilizados(): int
    {
        return $this->paseosUtilizados;
    }

    public function setPaseosUtilizados(int $paseosUtilizados): static
    {
        $this->paseosUtilizados = $paseosUtilizados;
        return $this;
    }

    public function getPaseosDisponibles(): int
    {
        return max(0, $this->paseosIncluidos - $this->paseosUtilizados);
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

    public function estaActiva(): bool
    {
        $now = new \DateTimeImmutable();
        return $this->estado === 'activa'
            && $this->fechaInicio !== null
            && $this->fechaFin !== null
            && $this->fechaInicio <= $now
            && $this->fechaFin >= $now
            && $this->getPaseosDisponibles() > 0;
    }

    public function __toString(): string
    {
        return $this->plan ? $this->plan->getNombre() : 'Suscripción #' . $this->id;
    }
}
