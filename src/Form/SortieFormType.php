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
use Symfony\Component\Form\FormInterface;
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
            ->add('description', TextareaType::class)
            ->add('nbInscriptionsMax')
            ->add('dateCloture', DateTimeType::class, [
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'placeholder' => 'Choisissez une ville',
                'choice_label' => 'nom'
            ]);

            $formModifier = function (FormInterface $form, Ville $ville = null) {
                $lieux = null === $ville ? [] : $this->entityManager->getRepository(Lieu::class)->findBy(['ville' => $ville]);

                $form->add('lieu', EntityType::class, [
                    'class' => Lieu::class,
                    'placeholder' => 'Choisissez un lieu',
                    'choices' => $lieux,
                ]);
            };

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function ($event) use ($formModifier){
                    $form = $event->getForm(); // The FormBuilder
                    $sortie = $event->getData(); // The Form Object (unused here)
                    $ville = $form->get('ville')->getData();

                    $formModifier($form, $ville);

            });

            $builder->get('ville')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use($formModifier){

                    $ville = $event->getForm()->get('ville')->getData();
                    
                    $formModifier($event->getForm()->getParent(), $ville);
                }
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'compound' => true,
            'inherit_data' => true,
        ]);
    }
}
