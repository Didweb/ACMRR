<?php
namespace App\Form\Product;

use App\Entity\Artist;
use App\Entity\ProductTag;
use App\Entity\RecordLabel;
use App\Entity\ProductTitle;
use App\Entity\ProductEdition;
use Symfony\Component\Form\AbstractType;
use App\ValueObject\Product\ProductFormat;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductEditionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', EntityType::class, [
                'class' => ProductTitle::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'd-none', // o 'display: none' en tu CSS
                ],
            ])
            ->add('label', EntityType::class, [
                'class' => RecordLabel::class,
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false, 
                'label' => 'Sello discográfico',
                'attr' => [
                    'class' => 'form-control mb-3',
                ],
            ])
            ->add('year', IntegerType::class, [
                'required' => false,
                'label' => 'Año de edición',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('format', ChoiceType::class, [
                    'choices' => ProductFormat::choices(),
                    'choice_label' => fn(ProductFormat $format) => $format->getValue(),
                    'choice_value' => fn(?ProductFormat $format) => $format?->getValue(),
                    'label' => 'Formato',
                    'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('stockNew', IntegerType::class, [
                'label' => 'Stock nuevo',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('priceNew', NumberType::class, [
                'label' => 'Precio estado nuevo',
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'step' => '0.10'],
            ])
            ->add('artists', EntityType::class, [
                'class' => Artist::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Artistas',
                'required' => false,
                'attr' => [
                    'class' => 'select2 form-control mb-3',
                ],
            ])
            ->add('tags', EntityType::class, [
            'class' => ProductTag::class,
            'choice_label' => 'name',   
            'multiple' => true,
            'expanded' => false,       
            'label' => 'Etiquetas',
            'required' => false,
            'attr' => [
                'class' => 'select2 form-control mb-3', 
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductEdition::class,
        ]);
    }
}