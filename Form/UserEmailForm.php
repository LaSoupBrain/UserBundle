<?php

namespace Dtw\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserEmailForm
 *
 * @package DtwCoreBundle\Form
 *
 * @author Justin Amargo
 */
class UserEmailForm extends AbstractType
{
    /**
     * Declaring Variables and creation of the form.
     *
     * @param FormBuilderInterface $builder allow you to write simple form "recipes" and have it do all the heavy-lifting of actually building the form.
     * @param array $options is the options of the form.
     *
     * @author Justin Amargo
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class
            )
            ->add(
                'send',
                SubmitType::class
            );
    }
}