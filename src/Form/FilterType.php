<?php


namespace App\Form;


use App\Entity\Site;
use App\Form\Model\Filter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false
            ])
            ->add('recherche', TextType::class)
            ->add('dateDebut', DateTimeType::class,[
                'label' => 'Entre',
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'et',
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ])
            ->add('organisateur', ChoiceType::class, [
                'choices' => [
                    'Sorties dont je suis l\'organisateur/trice' => true
                ],
                'expanded' => true,
                'multiple' => true
            ])
            ->add('inscrit', ChoiceType::class, [
                'choices' => [
                    'Sorties auxquelles je suis inscrit/e' => true
                ],
                'expanded' => true,
                'multiple' => true
            ])
            ->add('nonInscrit', ChoiceType::class, [
                'choices' => [
                    'Sorties auxquelles je ne suis pas inscrit/e' => true
                ],
                'expanded' => true,
                'multiple' => true
            ])
            ->add('sortiesPassees', ChoiceType::class, [
                'choices' => [
                    'Sorties passées' => true
                ],
                'expanded' => true,
                'multiple' => true
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
        ]);
    }
}