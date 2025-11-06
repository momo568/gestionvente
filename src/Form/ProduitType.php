<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex : Ordinateur portable'],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Description du produit...'],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (DT)',
                'attr' => ['class' => 'form-control', 'min' => 0, 'step' => 0.01],
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Stock disponible',
                'attr' => ['class' => 'form-control', 'min' => 0],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false, // car le fichier n’est pas directement lié à l’entité
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG ou PNG)',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
