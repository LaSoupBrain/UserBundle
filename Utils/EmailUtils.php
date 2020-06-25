<?php

namespace Dtw\UserBundle\Utils;

/**
 * This class is used for all sending of email.
 *
 * @package DtwCoreBundle\Utils
 *
 * @author Richard Soliven
 */
class EmailUtils
{
    private $templating;

    private $mailer;

    /**
     * @var $mailerName
     */
    private $mailerName;

    /**
     * @var $mailerEnail
     */
    private $mailerEmail;

    /**
     * EmailUtils constructor.
     *
     * @param string $mailerName this is the Name for email.
     * @param string $mailerEmail this is the email of sender.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $templating
     */
    public function __construct(string $mailerName, string $mailerEmail, \Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->mailerName = $mailerName;
        $this->mailerEmail = $mailerEmail;
    }

    /**
     * Email for reset password
     *
     * @param $email this is the email of the current user.
     * @param $entity user entity.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @author Richard Soliven
     *
     * @return int
     */
    public function resetPassword($email, $entity)
    {
        $message = (new \Swift_Message('Reset Password'))
            ->setSubject('Reset Password')
            ->setFrom($this->mailerEmail, $this->mailerName)
            ->setTo($email)
            ->setBody($this->templating->render(
                '@DtwUser/Email/reset_password_template.html.twig',
                array('user' => $entity)
            ),
                'text/html'
            );
        return $this->mailer->send($message);
    }

}