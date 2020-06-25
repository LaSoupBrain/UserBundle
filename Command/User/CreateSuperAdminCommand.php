<?php

namespace Dtw\UserBundle\Command\User;

use Dtw\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\{Input\InputInterface, Output\OutputInterface};
use Symfony\Component\Console\Question\Question;

/**
 * Class CreateSuperAdminCommand
 *
 * @package UserBundle\Command\User
 *
 * @author Ali, Muamar
 */
class CreateSuperAdminCommand extends ContainerAwareCommand
{
    /**
     * Attempts limit for inputting email.
     */
    const EMAIL_MAX_ATTEMPTS_LIMIT = 3;

    /**
     * Message to be displayed if successfully created
     */
    const SUCCESS_MESSAGE = 'Success: Super Admin user is created, if you want to update your information, you can login: "{hostname}/user/login" and go to the user and update.';

    /**
     * Message to be displayed in the inputting of email.
     */
    const QUESTION_EMAIL = "If leave this blank, it will set to default value: %s \nPlease enter your email: ";

    /**
     * Message to be displayed in the inputting of password.
     */
    const QUESTION_PASSWORD = "If leave this blank, it will set to default value: %s \nPlease enter your password. ";

    /**
     * Set name for the console command.
     *
     * @author Ali, Muamar
     *
     * @var string
     */
    protected static $defaultName = 'user:create-super-admin';

    /**
     * Configuration and registering of command.
     *
     * @author Ali, Muamar
     */
    protected function configure()
    {
        $this
            ->setDescription('Create a super admin.')
            ->setHelp('This command allows you to create a user with a role of super admin.');
    }

    /**
     * Executing the command.
     *
     * @param InputInterface $input is the interface implemented by all input classes.
     * @param OutputInterface $output is the interface implemented by all Output classes.
     *
     * @throws \Exception
     * @author Ali, Muamarience/sur-les-traces-des-impressionnistes-1
    </loc>
     *
     * @return int|void|null\Dtw\UserBundle\Command\User\Question
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $userManager = $this
                ->getContainer()
                ->get('manager.user');
            $helper = $this->getHelper('question');
            $emailQuestion = $this->emailQuestion();

            $userManager->validateEmail($emailQuestion);
            $emailQuestion->setMaxAttempts(self::EMAIL_MAX_ATTEMPTS_LIMIT);

            $email = $helper->ask($input, $output, $emailQuestion);
            $password = $helper->ask($input, $output, $this->passwordQuestion());

            $userManager->createAdminDefault(
                new User(),
                $email,
                $password
            );

            $output->writeln(self::SUCCESS_MESSAGE);
        } catch (\Exception $e) {
            throw new \Exception('There\'s an error creating a user with role of Super Admin.');
        }
    }
    
    /**
     * Getting the entered email.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return Question
     */
    public function emailQuestion()
    {
        try {
            $defaultEmail = User::USER_DEFAULT_EMAIL;

            return new Question(
                sprintf(self::QUESTION_EMAIL, $defaultEmail),
                $defaultEmail
            );
        } catch (\Exception $e) {
            throw new \Exception('An error occured at email.');
        }
    }

    /**
     * Getting the entered password.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return Question
     */
    public function passwordQuestion(): Question
    {
        try {
            $defaultPassword = User::USER_DEFAULT_PASSWORD;

            $passwordQuestion = new Question(
                sprintf(self::QUESTION_PASSWORD, $defaultPassword),
                $defaultPassword
            );

            $passwordQuestion
                ->setHidden(true)
                ->setHiddenFallback(false);
        } catch (\Exception $e) {
            throw new \Exception('An error occured at password.');
        }

        return $passwordQuestion;
    }
}