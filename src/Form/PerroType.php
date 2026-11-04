<?php

namespace App\Form;

use App\Entity\Perro;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class PerroType extends AbstractType
{
    public function __construct(
        private readonly UsuarioRepository $usuarios
    ) {
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'constraints' => [
                    new Assert\NotBlank(message: 'El nombre es obligatorio.'),
                    new Assert\Length(max: 80),
                ],
            ])
            ->add('raza', TextType::class, [
                'label' => 'Raza',
                'constraints' => [
                    new Assert\NotBlank(message: 'La raza es obligatoria.'),
                    new Assert\Length(max: 80),
                ],
            ])
            ->add('edad', IntegerType::class, [
                'label' => 'Edad (años)',
                'constraints' => [
                    new Assert\NotBlank(message: 'La edad es obligatoria.'),
                    new Assert\Range(
                        min: 0,
                        max: 30,
                        notInRangeMessage: 'La edad debe estar entre 0 y 30 años.'
                    ),
                ],
            ])
            ->add('tamano', ChoiceType::class, [
                'label' => 'Tamaño',
                'choices' => [
                    'Pequeño' => 'pequeno',
                    'Mediano' => 'mediano',
                    'Grande' => 'grande',
                ],
                'placeholder' => 'Selecciona un tamaño',
                'constraints' => [
                    new Assert\NotBlank(message: 'El tamaño es obligatorio.'),
                ],
            ])
            ->add('propietario', EntityType::class, [
                'label' => 'Propietario',
                'class' => Usuario::class,
                'choice_label' => function (Usuario $usuario) {
                    return $usuario->getNombreCompleto() . ' (' . $usuario->getEmail() . ')';
                },
                'query_builder' => $this->usuarios->createQueryBuilder('u')
                    ->where('u.roles LIKE :rol')
                    ->setParameter('rol', '%ROLE_CLIENTE%')
                    ->orderBy('u.apellido', 'ASC'),
                'placeholder' => 'Selecciona un propietario',
                'constraints' => [
                    new Assert\NotBlank(message: 'El propietario es obligatorio.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Perro::class,
        ]);
    }
}
