<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class)
            ->add('prenom', TextType::class)
            ->add('nom', TextType::class)
            ->add('telephone', TextType::class)
            ->add('mail', TextType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'required' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 6)),
                ),
                'first_options' => array('label' => 'Mot de passe :'),
                'second_options' => array('label' => 'Confirmation :'),
            ))
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false
            ])
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(
                        [
                            'maxSize' => '7024k',
                            'mimeTypesMessage' => "Image format not allowed !",
                        ]
                    )
                ]
            ]);
        if (in_array('ROLE_ADMIN', $options['role'])) {
            // do as you want if admin
            $builder
                ->add('actif');
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['create'],
            'role' => ['ROLE_USER'],
        ]);
    }
}
