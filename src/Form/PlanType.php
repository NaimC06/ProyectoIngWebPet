<?php

namespace App\Form;

use App\Entity\Plan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class PlanType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre del plan',
                'constraints' => [
                    new Assert\NotBlank(message: 'El nombre es obligatorio.'),
                    new Assert\Length(max: 80),
                ],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 500),
                ],
            ])
            ->add('precio', MoneyType::class, [
                'label' => 'Precio (USD)',
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'El precio es obligatorio.'),
                    new Assert\GreaterThan(
                        value: 0,
                        message: 'El precio debe ser mayor que cero.'
                    ),
                ],
            ])
            ->add('cantidadPaseos', IntegerType::class, [
                'label' => 'Cantidad de paseos',
                'constraints' => [
                    new Assert\NotBlank(message: 'La cantidad de paseos es obligatoria.'),
                    new Assert\GreaterThan(
                        value: 0,
                        message: 'Debe ser mayor que cero.'
                    ),
                ],
            ])
            ->add('duracionDias', IntegerType::class, [
                'label' => 'Duración (días)',
                'constraints' => [
                    new Assert\NotBlank(message: 'La duración es obligatoria.'),
                    new Assert\GreaterThan(
                        value: 0,
                        message: 'Debe ser mayor que cero.'
                    ),
                ],
            ])
            ->add('estado', ChoiceType::class, [
                'label' => 'Estado',
                'choices' => [
                    'Activo' => 'activo',
                    'Inactivo' => 'inactivo',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'El estado es obligatorio.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plan::class,
        ]);
    }
}
