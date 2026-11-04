<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UsuarioType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $usuario = $builder->getData();
        $esNuevo = $options['es_nuevo'];

        $rolActual = 'ROLE_CLIENTE';

        if ($usuario instanceof Usuario) {
            foreach ([
                'ROLE_ADMIN',
                'ROLE_PASEADOR',
                'ROLE_CLIENTE'
            ] as $rol) {
                if (in_array($rol, $usuario->getRoles(), true)) {
                    $rolActual = $rol;
                    break;
                }
            }
        }

        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'El nombre es obligatorio.'
                    ),
                    new Assert\Length(max: 100),
                ],
            ])

            ->add('apellido', TextType::class, [
                'label' => 'Apellido',
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'El apellido es obligatorio.'
                    ),
                    new Assert\Length(max: 100),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'El correo es obligatorio.'
                    ),
                    new Assert\Email(
                        message: 'Ingresa un correo electrónico válido.'
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

                    new Assert\Regex(
                        pattern: '/^[0-9]{10}$/D',
                        message: 'La cédula debe contener exactamente 10 dígitos.'
                    ),

                    new Assert\Callback(
                        static function (
                            mixed $valor,
                            ExecutionContextInterface $context
                        ): void {
                            if (
                                !is_string($valor)
                                || preg_match('/^[0-9]{10}$/D', $valor) !== 1
                            ) {
                                return;
                            }

                            $provincia = (int) substr($valor, 0, 2);
                            $tercerDigito = (int) $valor[2];

                            if (
                                $provincia < 1
                                || $provincia > 24
                                || $tercerDigito > 5
                            ) {
                                $context
                                    ->buildViolation(
                                        'La cédula tiene un código de provincia o tipo inválido.'
                                    )
                                    ->addViolation();

                                return;
                            }

                            $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
                            $suma = 0;

                            for ($i = 0; $i < 9; $i++) {
                                $producto = ((int) $valor[$i])
                                    * $coeficientes[$i];

                                if ($producto > 9) {
                                    $producto -= 9;
                                }

                                $suma += $producto;
                            }

                            $verificador = (10 - ($suma % 10)) % 10;
                            $ultimoDigito = (int) $valor[9];

                            if ($verificador !== $ultimoDigito) {
                                $context
                                    ->buildViolation(
                                        'La cédula no supera la validación del dígito verificador.'
                                    )
                                    ->addViolation();
                            }
                        }
                    ),
                ],
            ])

            ->add('rol', ChoiceType::class, [
                'label' => 'Tipo de usuario',
                'mapped' => false,
                'choices' => [
                    'Cliente' => 'ROLE_CLIENTE',
                    'Paseador' => 'ROLE_PASEADOR',
                    'Administrador' => 'ROLE_ADMIN',
                ],
                'data' => $rolActual,
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Selecciona un tipo de usuario.'
                    ),
                ],
            ])

            ->add('contrasenaNueva', PasswordType::class, [
                'label' => $esNuevo
                    ? 'Contraseña'
                    : 'Nueva contraseña (opcional)',
                'mapped' => false,
                'required' => $esNuevo,
                'always_empty' => true,
                'constraints' => $esNuevo
                    ? [
                        new Assert\NotBlank(
                            message: 'La contraseña es obligatoria.'
                        ),
                        new Assert\Length(
                            min: 10,
                            minMessage: 'La contraseña debe tener al menos 10 caracteres.'
                        ),
                    ]
                    : [
                        new Assert\Length(
                            min: 10,
                            minMessage: 'La nueva contraseña debe tener al menos 10 caracteres.'
                        ),
                    ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'es_nuevo' => false,
        ]);

        $resolver->setAllowedTypes('es_nuevo', 'bool');
    }
}