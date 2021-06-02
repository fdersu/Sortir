<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateDebut', DateTimeType::class, [
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ])
            ->add('duree', NumberType::class)
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'choice_label' => 'nom'
            ])

            ->addEventListener(FormEvents::POST_SET_DATA, function ($event) {
                $form = $event->getForm(); // The FormBuilder
                $sortie = $event->getData(); // The Form Object
                $ville = $form->get('ville')->getData();


               dump($ville);
               dump($sortie);

               $lieux = $this->entityManager->getRepository(Lieu::class)->findBy(['ville' => $ville]);

               $form->add('lieu', ChoiceType::class, [
                   'choices' => $lieux,
                   'choice_label' => 'nom'
                   ]);

               $event->setData($sortie);
            })

            ->add('description', TextareaType::class)
            ->add('nbInscriptionsMax')
            ->add('dateCloture', DateTimeType::class, [
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
