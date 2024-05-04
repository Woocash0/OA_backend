<?php

namespace App\Form;

use App\Entity\Warranty;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;

class WarrantyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('category', ChoiceType::class, [
            'choices' => [
                'Bike' => 'Bike',
                'Car' => 'Car',
                'Console' => 'Console',
                'Earphones' => 'Earphones',
                'Keyboard' => 'Keyboard',
                'Laptop' => 'Laptop',
                'Microphone' => 'Microphone',
                'Mouse' => 'Mouse',
                'Phone' => 'Phone',
                'Printer' => 'Printer',
                'Speakers' => 'Speakers',
                'TV' => 'TV'
            ],
            'attr' => [
                'class' => 'detail',
            ],
            'label' => 'Category',
        ])
        ->add('product_name', TextType::class, [
            'attr' => [
                'class' => 'detail',
                'placeholder' => 'e.g. Samsung OLED 4k 2020',
            ],
            'label' => 'Product Name',
        ])
        ->add('purchase_date', DateType::class, array(
            'widget' => 'single_text',
            'attr' => [
                'class' => 'detail',
            ],
            'label' => 'Purchase Date',
        ))
        ->add('warranty_period', IntegerType::class, [
            'attr' => [
                'class' => 'detail',
                'placeholder' => 'e.g. 5 lat',
            ],
            'constraints' => [
                new Assert\Range([
                    'min' => 0,
                    'max' => 100,
                    'notInRangeMessage' => 'Please enter a value between {{ min }} and {{ max }}',
                ]),
            ],
            'label' => 'Warranty Period',
            
        ])
        ->add('receipt', FileType::class, [
            'attr' => [
                'class' => 'detail',
                'id' => 'file-box'
            ],
            'label' => 'Receipt',
            'required'   => false,
            'mapped' => false,
            'constraints' => [
                new Assert\File([
                    'maxSize' => '2M',
                    'mimeTypes' => ['image/svg+xml', 'image/png', 'image/jpeg', 'image/jpg'],
                    'mimeTypesMessage' => 'Invalid file format. Allowed formats: .svg, .jpg, .png.',
                ]),
            ],
        ])
        ->add('tags', EntityType::class, [
            'class' => Tag::class,
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
            'attr' => [
                'class' => 'detail',
            ],
            'label' => 'Tags',
        ]);
        ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Warranty::class,
        ]);
    }
}
