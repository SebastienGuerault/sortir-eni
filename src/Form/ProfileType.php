<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


// Modifier le profil utilisateur

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Pseudonyme', null, ['label' => 'Pseudo'])
            ->add('prenom', null, ['label' => 'Prénom'])
            ->add('nom', null, ['label' => 'Nom'])
            ->add('telephone', null, ['label' => 'Téléphone'])
            ->add('email')
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe actuel'
                //La contrainte ci-dessous NE MARCHE PAS !!
//                'constraints' => [
//                    // vérification du mot de passe actuel
//                    new UserPassword([
//                        'message' => 'Votre mot de passe actuel est invalide !'
//                    ])
 //               ]
            ])
            ->add('new_password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas !',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe devrait avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('campus' , EntityType::class, [
                'label' => 'Votre campus',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])

            // Chargement des photos à revoir, ne marche pas
//            ->add('pictureUpload', FileType::class, [
//                'label' => 'Ma Photo',
//                'attr' => ['placeholder' => 'Sélectionnez votre photo']
//            ])
//            ->add('submit', SubmitType::class, ['label' => 'Télécharger votre photo'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
