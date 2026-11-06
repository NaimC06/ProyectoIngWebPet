<?php

namespace App\Form;

use App\Entity\Plan;
use App\Entity\Suscripcion;
use App\Entity\Usuario;
use App\Repository\PlanRepository;
use App\Repository\UsuarioRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SuscripcionType extends AbstractType
{
    public function __construct(
        private readonly UsuarioRepository $usuarios,
        private readonly PlanRepository $planes
    ) {
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('usuario', EntityType::class, [
                'label' => 'Cliente',
                'class' => Usuario::class,
                'choice_label' => function (Usuario $usuario) {
                    return $usuario->getNombreCompleto() . ' (' . $usuario->getEmail() . ')';
                },
                'query_builder' => $this->usuarios->createQueryBuilder('u')
                    ->where('u.roles LIKE :rol')
                    ->setParameter('rol', '%ROLE_CLIENTE%')
                    ->orderBy('u.apellido', 'ASC'),
                'placeholder' => 'Selecciona un cliente',
                'constraints' => [
                    new Assert\NotBlank(message: 'El cliente es obligatorio.'),
                ],
            ])
            ->add('plan', EntityType::class, [
                'label' => 'Plan',
                'class' => Plan::class,
                'choice_label' => function (Plan $plan) {
                    return sprintf('%s - $%s - %d paseos/%d días', $plan->getNombre(), $plan->getPrecio(), $plan->getCantidadPaseos(), $plan->getDuracionDias());
                },
                'query_builder' => $this->planes->createQueryBuilder('p')
                    ->where('p.estado = :estado')
                    ->setParameter('estado', 'activo')
                    ->orderBy('p.nombre', 'ASC'),
                'placeholder' => 'Selecciona un plan',
                'constraints' => [
                    new Assert\NotBlank(message: 'El plan es obligatorio.'),
                ],
            ])
            ->add('fechaInicio', DateType::class, [
                'label' => 'Fecha de inicio',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(message: 'La fecha de inicio es obligatoria.'),
                ],
            ])
            ->add('fechaFin', DateType::class, [
                'label' => 'Fecha de finalización',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(message: 'La fecha de finalización es obligatoria.'),
                ],
            ])
            ->add('paseosUtilizados', IntegerType::class, [
                'label' => 'Paseos utilizados',
                'constraints' => [
                    new Assert\NotBlank(message: 'Los paseos utilizados son obligatorios.'),
                    new Assert\GreaterThanOrEqual(
                        value: 0,
                        message: 'No puede ser negativo.'
                    ),
                ],
            ])
            ->add('estado', ChoiceType::class, [
                'label' => 'Estado',
                'choices' => [
                    'Activa' => 'activa',
                    'Cancelada' => 'cancelada',
                    'Vencida' => 'vencida',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'El estado es obligatorio.'),
                ],
            ]);

        // Validación personalizada: fechas coherentes y paseos no superen disponibles
        $builder->addEventListener(
            \Symfony\Component\Form\FormEvents::POST_SUBMIT,
            function (\Symfony\Component\Form\FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if (!$data instanceof Suscripcion) {
                    return;
                }

                // Validar que fechaFin >= fechaInicio
                if ($data->getFechaInicio() && $data->getFechaFin()) {
                    if ($data->getFechaFin() < $data->getFechaInicio()) {
                        $form->get('fechaFin')->addError(
                            new \Symfony\Component\Form\FormError(
                                'La fecha de finalización no puede ser anterior a la de inicio.')
                        );
                    }
                }

                // Validar que paseosUtilizados no superen paseosIncluidos
                if ($data->getPlan() && $data->getPaseosUtilizados() > $data->getPlan()->getCantidadPaseos()) {
                    $form->get('paseosUtilizados')->addError(
                        new \Symfony\Component\Form\FormError(
                            'Los paseos utilizados no pueden superar los incluidos en el plan.')
                    );
                }

                // Validar que el usuario tenga rol CLIENTE
                if ($data->getUsuario() && !in_array('ROLE_CLIENTE', $data->getUsuario()->getRoles(), true)) {
                    $form->get('usuario')->addError(
                        new \Symfony\Component\Form\FormError(
                            'El usuario seleccionado debe tener rol de cliente.')
                    );
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suscripcion::class,
        ]);
    }
}
