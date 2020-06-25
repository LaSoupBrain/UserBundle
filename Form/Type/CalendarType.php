<?php

    // Custom Form Field Type
    // AvatarType
    // author: John Kennith Mirano

    namespace Dtw\UserBundle\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\DateType;

    class CalendarType extends AbstractType {

        public function configurationOptions(OptionsResolver $resolver) {

            $resolver->setDefaults(array(
                'paymentDate'
            ));

        }

        public function getParent() {

            return DateType::class;

        }

    }