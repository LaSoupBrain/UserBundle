<?php

namespace Dtw\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * Class for changing password of user form
 *
 * @author Richard Soliven
 *
 * @package Dtw\UserBundle\Form
 */
class UserPasswordForm extends AbstractType
{
    /**
     * Declaring Variables and creation of the form.
     *
     * @param FormBuilderInterface $builder allow you to write simple form.
     * @param array $options is the options of the form.
     *
     * @author Richard Soliven
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'newPassword',
                RepeatedType::class, [
                    'first_options'  => array(
                        'label' => 'New password'
                    ),
                    'second_options' => array(
                        'label' => 'Confirm new Password'
                    ),
                    'invalid_message' => 'The password fields must match.',
                    'type' => PasswordType::class
                ]
            )
            ->add(
                'submit',
                SubmitType::class
            )
        ;
    }
}