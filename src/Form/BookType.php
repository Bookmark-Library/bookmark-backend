<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('editor', TextType::class, [
                'label' => 'Editeur'
            ])
            ->add('collection', TextType::class, [
                'label' => 'Collection'
            ])
            ->add('summary', TextType::class, [
                'label' => 'Résumé'
            ])
            ->add('isbn', IntegerType::class, [
                'label' => 'Isbn'
            ])
            ->add('pages', IntegerType::class, [
                'label' => 'Pages'
            ])
            ->add('price', TextType::class, [
                'label' => 'Prix'
            ])
            ->add('image', UrlType::class, [
                'label' => 'Couverture'
            ])
            ->add('publicationDate', IntegerType::class, [
                'label' => 'Année de publication'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
