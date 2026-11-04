<?php

namespace App\Command;

use App\Entity\Paseo;
use App\Entity\Perro;
use App\Entity\Plan;
use App\Entity\Suscripcion;
use App\Entity\Usuario;
use App\Repository\PerroRepository;
use App\Repository\PlanRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:seed-demo-suscripciones-paseos',
    description: 'Crea suscripciones y paseos de demostración para análisis.'
)]
final class SeedDemoSuscripcionesPaseosCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UsuarioRepository $usuarios,
        private readonly PlanRepository $planes,
        private readonly PerroRepository $perros
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $clientes = $this->usuarios->findClientes();
        $paseadores = $this->usuarios->findBy(['roles' => 'ROLE_PASEADOR'], ['id' => 'ASC']);
        $planes = $this->planes->findActivos();

        if (count($clientes) === 0 || count($planes) === 0) {
            $output->writeln('<error>Faltan clientes o planes. Ejecuta primero los seeds de usuarios y planes.</error>');
            return Command::FAILURE;
        }

        // Crear suscripciones para algunos clientes
        $suscripcionesCreadas = 0;
        foreach ($clientes as $index => $cliente) {
            if ($index >= 4) break; // Solo 4 clientes con suscripción

            $plan = $planes[$index % count($planes)];
            $perrosDelCliente = $this->perros->findBy(['propietario' => $cliente]);

            if (empty($perrosDelCliente)) continue;

            $suscripcionExistente = $this->entityManager->getRepository(Suscripcion::class)->findOneBy([
                'usuario' => $cliente,
                'plan' => $plan,
            ]);

            if ($suscripcionExistente !== null) continue;

            $fechaInicio = new \DateTimeImmutable('-30 days');
            $fechaFin = new \DateTimeImmutable('+30 days');

            $suscripcion = new Suscripcion();
            $suscripcion->setUsuario($cliente);
            $suscripcion->setPlan($plan);
            $suscripcion->setFechaInicio($fechaInicio);
            $suscripcion->setFechaFin($fechaFin);
            $suscripcion->setPaseosUtilizados(random_int(0, $plan->getCantidadPaseos()));
            $suscripcion->setEstado('activa');

            $this->entityManager->persist($suscripcion);
            $suscripcionesCreadas++;
        }

        $this->entityManager->flush();

        // Crear paseos para paseadores
        $paseosCreados = 0;
        $estados = ['programado', 'en_curso', 'completado', 'cancelado'];
        $precios = ['15.00', '18.00', '20.00', '22.00', '25.00'];

        foreach ($paseadores as $paseador) {
            $clientesConSuscripcion = array_filter($clientes, function ($c) {
                $subs = $this->entityManager->getRepository(Suscripcion::class)->findByUsuario($c->getId());
                return !empty($subs);
            });

            $clientesArray = array_values($clientesConSuscripcion);

            for ($i = 0; $i < 3; $i++) {
                if (empty($clientesArray)) break;

                $cliente = $clientesArray[array_rand($clientesArray)];
                $perrosDelCliente = $this->perros->findBy(['propietario' => $cliente]);

                if (empty($perrosDelCliente)) continue;

                $perro = $perrosDelCliente[array_rand($perrosDelCliente)];
                $fechaHora = new \DateTimeImmutable(sprintf('-%d days %d:%d:00', random_int(0, 30), random_int(8, 18), random_int(0, 3) * 15));
                $estado = $estados[array_rand($estados)];
                $precio = $precios[array_rand($precios)];

                $paseo = new Paseo();
                $paseo->setCliente($cliente);
                $paseo->setPerro($perro);
                $paseo->setPaseador($paseador);
                $paseo->setFechaHora($fechaHora);
                $paseo->setPrecio($estado === 'completado' ? $precio : null);
                $paseo->setEstado($estado);
                $paseo->setObservaciones('Paseo de demostración generado automáticamente.');

                $this->entityManager->persist($paseo);
                $paseosCreados++;
            }
        }

        // Crear algunos paseos adicionales para clientes sin paseador asignado
        foreach ($clientes as $cliente) {
            $perrosDelCliente = $this->perros->findBy(['propietario' => $cliente]);
            if (empty($perrosDelCliente)) continue;

            for ($i = 0; $i < 2; $i++) {
                $perro = $perrosDelCliente[array_rand($perrosDelCliente)];
                $fechaHora = new \DateTimeImmutable(sprintf('-%d days %d:%d:00', random_int(0, 60), random_int(8, 18), random_int(0, 3) * 15));
                $estado = $estados[array_rand($estados)];
                $precio = $precios[array_rand($precios)];

                $paseo = new Paseo();
                $paseo->setCliente($cliente);
                $paseo->setPerro($perro);
                $paseo->setPaseador(null);
                $paseo->setFechaHora($fechaHora);
                $paseo->setPrecio($estado === 'completado' ? $precio : null);
                $paseo->setEstado($estado);
                $paseo->setObservaciones('Paseo solicitado por cliente.');

                $this->entityManager->persist($paseo);
                $paseosCreados++;
            }
        }

        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln('<info>PET CONCIERGE - SUSCRIPCIONES Y PASEOS DEMO</info>');
        $output->writeln('Suscripciones nuevas: ' . $suscripcionesCreadas);
        $output->writeln('Paseos nuevos: ' . $paseosCreados);
        $output->writeln('');

        return Command::SUCCESS;
    }
}
