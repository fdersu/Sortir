<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('duree')
            ->add('description', TextareaType::class)
            ->add('nbInscriptionsMax')
            ->add('dateCloture', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('site', TextType::class, [
                'data' => $options['site'],
                'disabled' => true,
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'placeholder' => 'Choisissez une ville',
                'choice_label' => 'nom'
            ])

            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'mapped' => false,
                'placeholder' => 'Choisissez un lieu',
                'choice_label' => 'nom'
            ])
        ;

        /*
            $formModifier = function (FormInterface $form, Ville $ville) {

                $form->add('lieu', EntityType::class, array(
                    'class' => Lieu::class,
                    'query_builder' => function (EntityRepository $er) use ($ville) {
                        return $er->createQueryBuilder('u')
                            ->select('u')
                            ->where('u.ville = :ville')
                            ->setParameter('ville', $ville);
                    },
                    'choice_label' => 'nom')
                );
            };

            $builder->get('ville')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use($formModifier){
                    $ville = $event->getForm()->getParent()->get('ville')->getData();

                    $formModifier($event->getForm()->getParent(), $ville);
                }
            );
        */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
        $resolver->setRequired('site');
    }
}
