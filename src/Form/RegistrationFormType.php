<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'name',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Your name should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
                'label' => false,
            ])
            ->add('surname', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'surname',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a surname',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Your surname should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
                'label' => false,
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'email',
                ],
                'label' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'password',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
        
        ->add('confirmedPassword', PasswordType::class, [
            'mapped' => false,
            'label' => false,
            'attr' => [
                'autocomplete' => 'new-password',
                'placeholder' => ' confirm password',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 4096,
                ]),
            ],
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
