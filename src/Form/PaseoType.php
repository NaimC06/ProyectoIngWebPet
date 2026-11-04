<?php

namespace App\Form;

use App\Entity\Paseo;
use App\Entity\Perro;
use App\Entity\Usuario;
use App\Repository\PerroRepository;
use App\Repository\UsuarioRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

final class PaseoType extends AbstractType
{
    public function __construct(
        private readonly UsuarioRepository $usuarios,
        private readonly PerroRepository $perros
    ) {
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('cliente', EntityType::class, [
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
            ->add('perro', EntityType::class, [
                'label' => 'Perro',
                'class' => Perro::class,
                'choice_label' => function (Perro $perro) {
                    return $perro->getNombre() . ' (' . $perro->getRaza() . ')';
                },
                'query_builder' => $this->perros->createQueryBuilder('p')
                    ->where('1=0'), // Vacío inicialmente, se llena via JS
                'placeholder' => 'Primero selecciona un cliente',
                'disabled' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'El perro es obligatorio.'),
                ],
            ])
            ->add('paseador', EntityType::class, [
                'label' => 'Paseador',
                'class' => Usuario::class,
                'choice_label' => function (Usuario $usuario) {
                    return $usuario->getNombreCompleto() . ' (' . $usuario->getEmail() . ')';
                },
                'query_builder' => $this->usuarios->createQueryBuilder('u')
                    ->where('u.roles LIKE :rol')
                    ->setParameter('rol', '%ROLE_PASEADOR%')
                    ->orderBy('u.apellido', 'ASC'),
                'placeholder' => 'Selecciona un paseador (opcional)',
                'required' => false,
            ])
            ->add('fechaHora', DateTimeType::class, [
                'label' => 'Fecha y hora',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(message: 'La fecha y hora son obligatorias.'),
                ],
            ])
            ->add('duracion', IntegerType::class, [
                'label' => 'Duración (minutos)',
                'required' => false,
                'constraints' => [
                    new Assert\Range(
                        min: 15,
                        max: 180,
                        notInRangeMessage: 'La duración debe estar entre 15 y 180 minutos.'
                    ),
                ],
            ])
            ->add('precio', MoneyType::class, [
                'label' => 'Precio (USD)',
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'required' => false,
                'constraints' => [
                    new Assert\GreaterThanOrEqual(
                        value: 0,
                        message: 'El precio no puede ser negativo.'
                    ),
                ],
            ])
            ->add('estado', ChoiceType::class, [
                'label' => 'Estado',
                'choices' => [
                    'Programado' => 'programado',
                    'En curso' => 'en_curso',
                    'Completado' => 'completado',
                    'Cancelado' => 'cancelado',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'El estado es obligatorio.'),
                ],
            ])
            ->add('observaciones', TextareaType::class, [
                'label' => 'Observaciones',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 1000),
                ],
            ]);

        // Event listener para actualizar el dropdown de perros basado en el cliente seleccionado
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (!isset($data['cliente']) || !$data['cliente']) {
                    return;
                }

                $clienteId = $data['cliente'];
                $perrosDelCliente = $this->perros->findByPropietario((int) $clienteId);

                $choices = [];
                foreach ($perrosDelCliente as $perro) {
                    $choices[$perro->getNombre() . ' (' . $perro->getRaza() . ')'] = $perro->getId();
                }

                $form->add('perro', EntityType::class, [
                    'label' => 'Perro',
                    'class' => Perro::class,
                    'choice_label' => function (Perro $perro) {
                        return $perro->getNombre() . ' (' . $perro->getRaza() . ')';
                    },
                    'choices' => $perrosDelCliente,
                    'placeholder' => 'Selecciona un perro',
                    'disabled' => false,
                    'constraints' => [
                        new Assert\NotBlank(message: 'El perro es obligatorio.'),
                    ],
                ]);
            }
        );

        // Validación Back-End: perro pertenece al cliente
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if (!$data instanceof Paseo) {
                    return;
                }

                // Validar que el perro pertenezca al cliente
                if ($data->getCliente() && $data->getPerro()) {
                    if ($data->getPerro()->getPropietario()->getId() !== $data->getCliente()->getId()) {
                        $form->get('perro')->addError(
                            new \Symfony\Component\Form\FormError(
                                'El perro seleccionado no pertenece al cliente indicado.',
                                [],
                                null,
                                null
                            )
                        );
                    }
                }

                // Validar que el paseador tenga ROLE_PASEADOR
                if ($data->getPaseador() && !in_array('ROLE_PASEADOR', $data->getPaseador()->getRoles(), true)) {
                    $form->get('paseador')->addError(
                        new \Symfony\Component\Form\FormError(
                            'El usuario seleccionado debe tener rol de paseador.',
                            [],
                            null,
                            null
                        )
                    );
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paseo::class,
        ]);
    }
}
