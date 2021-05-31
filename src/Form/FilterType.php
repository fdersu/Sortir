<?php


namespace App\Form;


use App\Form\Model\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false
            ])
            ->add('recherche', TextType::class)
            ->add('dateDebut', DateTimeType::class,[
                'label' => 'Entre'
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'et'
            ])
            ->add('organisateur', ChoiceType::class, [
                'choices' => [
                    'Sorties dont je suis l\'organisateur/trice' => true
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('inscrit', ChoiceType::class, [
                'choices' => [
                    'Sorties auxquelles je suis inscrit/e' => true
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('nonInscrit', ChoiceType::class, [
                'choices' => [
                    'Sorties auxquelles je ne suis pas inscrit/e' => true
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('sortiesPassees', ChoiceType::class, [
                'choices' => [
                    'Sorties passée' => true
                ],
                'expanded' => true,
                'multiple' => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
        ]);
    }
}