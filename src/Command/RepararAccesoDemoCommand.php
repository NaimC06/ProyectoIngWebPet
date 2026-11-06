<?php

namespace App\Command;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:reparar-acceso-demo',
    description: 'Sincroniza las contraseñas de las cuentas demo.'
)]
final class RepararAccesoDemoCommand extends Command
{
    private const CORREOS_DEMO = [
        'admin@petconcierge.test',
        'cliente1@petconcierge.test',
        'cliente2@petconcierge.test',
        'cliente3@petconcierge.test',
        'cliente4@petconcierge.test',
        'cliente5@petconcierge.test',
        'paseador1@petconcierge.test',
        'paseador2@petconcierge.test',
    ];

    public function __construct(
        private readonly UsuarioRepository $usuarios,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $clave = getenv('PET_CONCIERGE_DEMO_PASSWORD');

        if (!is_string($clave) || strlen($clave) < 10) {
            $output->writeln(
                '<error>La variable PET_CONCIERGE_DEMO_PASSWORD no existe o tiene menos de 10 caracteres.</error>'
            );

            return Command::FAILURE;
        }

        $actualizados = 0;
        $faltantes = 0;

        foreach (self::CORREOS_DEMO as $correo) {
            $usuario = $this->usuarios->findOneBy([
                'email' => $correo,
            ]);

            if ($usuario === null) {
                $faltantes++;
                $output->writeln(
                    '<comment>No encontrado: ' . $correo . '</comment>'
                );
                continue;
            }

            $hash = $this->passwordHasher->hashPassword(
                $usuario,
                $clave
            );

            $usuario->setPassword($hash);
            $usuario->setActivo(true);

            if ($correo === 'admin@petconcierge.test') {
                $usuario->setRoles(['ROLE_ADMIN']);
            }

            $actualizados++;
        }

        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln('<info>REPARACION COMPLETADA</info>');
        $output->writeln('Cuentas actualizadas: ' . $actualizados);
        $output->writeln('Cuentas no encontradas: ' . $faltantes);
        $output->writeln('');
        $output->writeln(
            'Las contraseñas demo coinciden ahora con la variable de Railway.'
        );
        $output->writeln(
            'No se modificaron perros, planes ni suscripciones.'
        );

        return $actualizados > 0
            ? Command::SUCCESS
            : Command::FAILURE;
    }
}