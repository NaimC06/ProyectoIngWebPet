<?php

namespace App\Command;

use App\Entity\Perro;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:seed-demo-perros',
    description: 'Carga los perros de demostración de Pet Concierge.'
)]
final class SeedDemoPerrosCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UsuarioRepository $usuarios
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $clientes = $this->usuarios->findClientes();

        if (count($clientes) < 5) {
            $output->writeln('<error>No hay suficientes clientes. Ejecuta primero app:seed-demo-users.</error>');
            return Command::FAILURE;
        }

        $perrosData = [
            ['propietario_email' => 'cliente1@petconcierge.test', 'nombre' => 'Luna', 'raza' => 'Golden Retriever', 'edad' => 3, 'tamano' => 'grande'],
            ['propietario_email' => 'cliente1@petconcierge.test', 'nombre' => 'Max', 'raza' => 'Labrador', 'edad' => 2, 'tamano' => 'grande'],
            ['propietario_email' => 'cliente2@petconcierge.test', 'nombre' => 'Rocky', 'raza' => 'Bulldog Francés', 'edad' => 4, 'tamano' => 'mediano'],
            ['propietario_email' => 'cliente2@petconcierge.test', 'nombre' => 'Bella', 'raza' => 'Poodle', 'edad' => 1, 'tamano' => 'pequeno'],
            ['propietario_email' => 'cliente3@petconcierge.test', 'nombre' => 'Toby', 'raza' => 'Border Collie', 'edad' => 5, 'tamano' => 'mediano'],
            ['propietario_email' => 'cliente3@petconcierge.test', 'nombre' => 'Maya', 'raza' => 'Cocker Spaniel', 'edad' => 2, 'tamano' => 'mediano'],
            ['propietario_email' => 'cliente4@petconcierge.test', 'nombre' => 'Bruno', 'raza' => 'Pastor Alemán', 'edad' => 3, 'tamano' => 'grande'],
            ['propietario_email' => 'cliente5@petconcierge.test', 'nombre' => 'Kira', 'raza' => 'Shiba Inu', 'edad' => 2, 'tamano' => 'mediano'],
        ];

        $creados = 0;
        $existentes = 0;

        foreach ($perrosData as $datos) {
            $propietario = $this->usuarios->findOneBy(['email' => $datos['propietario_email']]);

            if (!$propietario) {
                $output->writeln(sprintf('<error>Propietario no encontrado: %s</error>', $datos['propietario_email']));
                continue;
            }

            $perroExistente = $this->entityManager->getRepository(Perro::class)->findOneBy([
                'nombre' => $datos['nombre'],
                'propietario' => $propietario,
            ]);

            if ($perroExistente !== null) {
                $existentes++;
                continue;
            }

            $perro = new Perro();
            $perro->setPropietario($propietario);
            $perro->setNombre($datos['nombre']);
            $perro->setRaza($datos['raza']);
            $perro->setEdad($datos['edad']);
            $perro->setTamano($datos['tamano']);

            $this->entityManager->persist($perro);
            $creados++;
        }

        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln('<info>PET CONCIERGE - PERROS DEMO</info>');
        $output->writeln('Perros nuevos: ' . $creados);
        $output->writeln('Perros que ya existían: ' . $existentes);
        $output->writeln('');

        return Command::SUCCESS;
    }
}
