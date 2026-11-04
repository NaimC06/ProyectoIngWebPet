<?php

namespace App\Command;

use App\Entity\Plan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:seed-demo-planes',
    description: 'Carga los planes prepagados de demostración de Pet Concierge.'
)]
final class SeedDemoPlanesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $planesData = [
            ['nombre' => 'Semana Pet', 'precio' => '25.00', 'cantidadPaseos' => 7, 'duracionDias' => 7],
            ['nombre' => 'Plan 2X', 'precio' => '45.00', 'cantidadPaseos' => 8, 'duracionDias' => 30],
            ['nombre' => 'Plan 3X', 'precio' => '60.00', 'cantidadPaseos' => 12, 'duracionDias' => 30],
            ['nombre' => 'Plan Laboral', 'precio' => '90.00', 'cantidadPaseos' => 20, 'duracionDias' => 30],
        ];

        $creados = 0;
        $existentes = 0;

        foreach ($planesData as $datos) {
            $planExistente = $this->entityManager->getRepository(Plan::class)->findOneBy([
                'nombre' => $datos['nombre'],
            ]);

            if ($planExistente !== null) {
                $existentes++;
                continue;
            }

            $plan = new Plan();
            $plan->setNombre($datos['nombre']);
            $plan->setPrecio($datos['precio']);
            $plan->setCantidadPaseos($datos['cantidadPaseos']);
            $plan->setDuracionDias($datos['duracionDias']);
            $plan->setEstado('activo');

            $this->entityManager->persist($plan);
            $creados++;
        }

        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln('<info>PET CONCIERGE - PLANES DEMO</info>');
        $output->writeln('Planes nuevos: ' . $creados);
        $output->writeln('Planes que ya existían: ' . $existentes);
        $output->writeln('');

        return Command::SUCCESS;
    }
}
