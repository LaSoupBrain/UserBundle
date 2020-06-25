<?php

namespace  Dtw\UserBundle\Form;

use Dtw\UserBundle\Entity\User;
use Dtw\UserBundle\Form\Type\CalendarType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;


/**
 * Class for adding user form
 *
 * @package Dtw\UserBundle\Form
 *
 * @author Richard Soliven
 */
class UserForm extends AbstractType
{
    /**
     * Declaring Variables and creation of the form.
     *
     * @param FormBuilderInterface $builder allow you to write simple form "recipes" and have it do all the heavy-lifting of actually building the form.
     * @param array $options is the options of the form.
     *
     * @author Richard Soliven
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'password',
                RepeatedType::class, [
                    'first_options' => array(
                        'label' => 'Password'
                    ),
                    'second_options' => array(
                        'label' => 'Confirm Password'
                    ),
                    'invalid_message' => 'The password fields must match.',
                    'type' => PasswordType::class
                ]
            )
            ->add('roles',
                ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => [
                        'SuperAdmin' => 'ROLE_SUPER-ADMIN',
                        'Admin' => 'ROLE_ADMIN',
                        'Moderator' => 'ROLE_MODERATOR',
                        'Creator' => 'ROLE_CREATOR',
                        'User' => 'ROLE_USER',
                    ],
                ]
            )
            ->add(
                'avatar',
                FileType::class, array(
                    'data_class' => null,
                    'constraints' => array(
                        new Image(array(
                                'mimeTypes' => array(
                                    User::MIME_TYPE_JPEG,
                                    User::MIME_TYPE_PNG
                                ),
                                'mimeTypesMessage' => User::IMAGE_MIME_ERROR_MESSAGE
                            )
                        )
                    )
                )
            )
            ->add(
                'hoverAvatar',
                FileType::class, array(
                    'data_class' => null,
                    'constraints' => array(
                        new Image(array(
                                'mimeTypes' => array(
                                    User::MIME_TYPE_JPEG,
                                    User::MIME_TYPE_PNG
                                ),
                                'mimeTypesMessage' => User::IMAGE_MIME_ERROR_MESSAGE
                            )
                        )
                    )
                )
            )
            ->add(
                'weight',
                TextType::class
            )
            ->add(
                'firstName',
                TextType::class
            )
            ->add(
                'middleName',
                TextType::class
            )
            ->add(
                'lastName',
                TextType::class
            )
            ->add(
                'designation',
                TextType::class
            )
            ->add(
                'startedAt',
                CalendarType::class,
                array(
                  'widget' => 'single_text'
                )
            )
            ->add(
                'location',
                TextType::class
            )
            ->add(
                'skype',
                TextType::class
            )
            ->add(
                'slack',
                TextType::class
            )
            ->add(
                'email',
                EmailType::class
            )
            ->add(
                'description',
                TextareaType::class
            )
            ->add(
                'submit',
                SubmitType::class
            );
    }
}