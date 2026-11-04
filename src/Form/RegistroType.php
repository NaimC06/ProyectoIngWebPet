<?php

namespace App\Form;

use App\Validator\CedulaEcuatoriana;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class RegistroType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('tipoCuenta', ChoiceType::class, [
                'label' => 'Quiero registrarme como',
                'placeholder' => 'Selecciona una opción',
                'choices' => [
                    'Cliente - Tengo perros' => 'ROLE_CLIENTE',
                    'Paseador - Quiero ofrecer paseos' => 'ROLE_PASEADOR',
                ],
                'attr' => ['data-registro-tipo' => ''],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Selecciona el tipo de cuenta.'
                    ),
                ],
            ])

            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'constraints' => [
                    new Assert\NotBlank(message: 'Ingresa tu nombre.'),
                    new Assert\Length(min: 2, max: 100),
                ],
            ])

            ->add('apellido', TextType::class, [
                'label' => 'Apellido',
                'constraints' => [
                    new Assert\NotBlank(message: 'Ingresa tu apellido.'),
                    new Assert\Length(min: 2, max: 100),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'constraints' => [
                    new Assert\NotBlank(message: 'Ingresa tu correo.'),
                    new Assert\Email(
                        message: 'El correo electrónico no es válido.'
                    ),
                    new Assert\Length(max: 180),
                ],
            ])

            ->add('cedula', TextType::class, [
                'label' => 'Cédula ecuatoriana',
                'attr' => [
                    'maxlength' => 10,
                    'inputmode' => 'numeric',
                    'placeholder' => '10 dígitos',
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'La cédula es obligatoria.'
                    ),
                    new Assert\Callback(
                        static function (
                            mixed $valor,
                            ExecutionContextInterface $context,
                            mixed $payload
                        ): void {
                            if (!is_string($valor) || $valor === '') {
                                return;
                            }

                            if (!CedulaEcuatoriana::esValida($valor)) {
                                $context->buildViolation(
                                    'La cédula ecuatoriana no es válida.'
                                )->addViolation();
                            }
                        }
                    ),
                ],
            ])

            ->add('telefono', TextType::class, [
                'label' => 'Celular',
                'attr' => [
                    'maxlength' => 10,
                    'placeholder' => '09XXXXXXXX',
                    'inputmode' => 'tel',
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'El celular es obligatorio.'
                    ),
                    new Assert\Regex(
                        pattern: '/^09[0-9]{8}$/D',
                        message: 'Ingresa un celular ecuatoriano válido de 10 dígitos.'
                    ),
                ],
            ])

            ->add('clave', PasswordType::class, [
                'label' => 'Contraseña',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'La contraseña es obligatoria.'
                    ),
                    new Assert\Length(
                        min: 10,
                        max: 72,
                        minMessage: 'Utiliza al menos 10 caracteres.'
                    ),
                ],
            ])

            ->add('perroNombre', TextType::class, [
                'label' => 'Nombre de tu perro',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 80),
                ],
            ])

            ->add('perroRaza', TextType::class, [
                'label' => 'Raza',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 80),
                ],
            ])

            ->add('perroEdad', IntegerType::class, [
                'label' => 'Edad en años (0 si es cachorro)',
                'required' => false,
                'invalid_message' => 'Ingresa una edad numérica válida.',
                'constraints' => [
                    new Assert\Range(
                        min: 0,
                        max: 25,
                        notInRangeMessage: 'La edad debe estar entre 0 y 25 años.'
                    ),
                ],
            ])

            ->add('perroTamano', ChoiceType::class, [
                'label' => 'Tamaño del perro',
                'required' => false,
                'placeholder' => 'Selecciona un tamaño',
                'choices' => [
                    'Pequeño' => 'pequeno',
                    'Mediano' => 'mediano',
                    'Grande' => 'grande',
                ],
            ])

            ->add('zonaTrabajo', ChoiceType::class, [
                'label' => 'Zona donde ofrecerás paseos',
                'required' => false,
                'placeholder' => 'Selecciona tu zona',
                'choices' => [
                    'La Carolina / Iñaquito' => 'La Carolina / Iñaquito',
                    'Quicentro / El Batán' => 'Quicentro / El Batán',
                    'Ponceano / Cotocollao' => 'Ponceano / Cotocollao',
                    'Carcelén / Calderón' => 'Carcelén / Calderón',
                    'Pomasqui / Mitad del Mundo' => 'Pomasqui / Mitad del Mundo',
                    'Centro / Mariscal' => 'Centro / Mariscal',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true,
            'csrf_token_id' => 'registro_pet_concierge',
        ]);
    }
}