<?php

namespace App\Command;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-demo-users',
    description: 'Carga las ocho cuentas de demostración de Pet Concierge.'
)]
final class SeedDemoUsersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UsuarioRepository $usuarios,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $password = getenv('PET_CONCIERGE_DEMO_PASSWORD');

        if (!is_string($password) || strlen($password) < 10) {
            $output->writeln(
                '<error>Configura PET_CONCIERGE_DEMO_PASSWORD con al menos 10 caracteres.</error>'
            );

            return Command::FAILURE;
        }

        $cuentas = [
            [
                'nombre' => 'Administrador',
                'apellido' => 'Pet Concierge',
                'email' => 'admin@petconcierge.test',
                'rol' => 'ROLE_ADMIN',
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Torres',
                'email' => 'cliente1@petconcierge.test',
                'rol' => 'ROLE_CLIENTE',
            ],
            [
                'nombre' => 'Diego',
                'apellido' => 'Ruiz',
                'email' => 'cliente2@petconcierge.test',
                'rol' => 'ROLE_CLIENTE',
            ],
            [
                'nombre' => 'Camila',
                'apellido' => 'Perez',
                'email' => 'cliente3@petconcierge.test',
                'rol' => 'ROLE_CLIENTE',
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Mora',
                'email' => 'cliente4@petconcierge.test',
                'rol' => 'ROLE_CLIENTE',
            ],
            [
                'nombre' => 'Sofia',
                'apellido' => 'Vega',
                'email' => 'cliente5@petconcierge.test',
                'rol' => 'ROLE_CLIENTE',
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rivas',
                'email' => 'paseador1@petconcierge.test',
                'rol' => 'ROLE_PASEADOR',
            ],
            [
                'nombre' => 'Valeria',
                'apellido' => 'Cruz',
                'email' => 'paseador2@petconcierge.test',
                'rol' => 'ROLE_PASEADOR',
            ],
        ];

        $creados = 0;
        $existentes = 0;

        foreach ($cuentas as $datos) {
            $usuarioExistente = $this->usuarios->findOneBy([
                'email' => $datos['email'],
            ]);

            if ($usuarioExistente !== null) {
                $existentes++;
                continue;
            }

            $usuario = new Usuario();

            $usuario->setNombre($datos['nombre']);
            $usuario->setApellido($datos['apellido']);
            $usuario->setEmail($datos['email']);
            $usuario->setRoles([$datos['rol']]);
            $usuario->setActivo(true);

            $hash = $this->passwordHasher->hashPassword(
                $usuario,
                $password
            );

            $usuario->setPassword($hash);

            $this->entityManager->persist($usuario);

            $creados++;
        }

        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln('<info>PET CONCIERGE - DATOS DEMO</info>');
        $output->writeln('Usuarios nuevos: ' . $creados);
        $output->writeln('Usuarios que ya existían: ' . $existentes);
        $output->writeln('');
        $output->writeln('Administrador: admin@petconcierge.test');
        $output->writeln('Clientes: cliente1 a cliente5@petconcierge.test');
        $output->writeln('Paseadores: paseador1 y paseador2@petconcierge.test');
        $output->writeln('');
        $output->writeln(
            '<comment>Las contraseñas se guardaron como hashes seguros.</comment>'
        );

        return Command::SUCCESS;
    }
}