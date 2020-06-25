<?php

namespace Dtw\UserBundle\Controller;

use Dtw\UserBundle\Entity\User;
use Dtw\UserBundle\Form\RegistrationForm;
use Dtw\UserBundle\Form\UserEmailForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController
 *
 * @package DtwAdminBundle\Controller
 *
 * @author Richard Soliven
 */
class SecurityController extends Controller
{
    /**
     * Message for error.
     */
    const ERROR_MESSAGE = 'There was an error occurred. Please kindly contact PHP DEVELOPER TEAM';

    /**
     * Admin dashboard.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@DtwUser/User/list.html.twig');
    }

    /**
     * Login
     *
     * @param Request $request
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('dtw_user_index'));
        }
        $authUtils = $this->get('security.authentication_utils');
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        return $this->render('@DtwUser/Security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * Logout
     *
     * @param Request $request
     *
     * @author Richard Soliven
     */
    public function logoutAction(Request $request)
    {
        $this->get('request')->getSession()->invalidate();
    }

    /**
     * To render the forgot password page.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forgotPasswordAction()
    {
        $formCreate = $this->createForm(
            UserEmailForm::class
        );

        return $this->render(
            '@DtwUser/Security/forgot-password.html.twig',
            array(
                'formCreate' => $formCreate->createView()
            )
        );
    }

    /**
     * Sending an email.
     *
     * @param Request $request handle the information about the client request.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendResetPasswordEmailAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            try {
                $userManager = $this->get('manager.user');
                $user = $userManager->isEmailExist($request->get("_email"));

                if ($user == null) {
                    $this->addFlash(
                        'error',
                        'Email not exist in the database');
                } else {
                    $userManager->resetPasswordEmail($user);

                    $response = $this->render('@DtwUser/Security/email_sent.html.twig');
                }
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    'An error occurred, please contact the PHP Team dev.');
            }
        }

        return empty($response) ? $this->redirectToRoute('dtw_user_forgot_password') : $response;
    }

    /**
     * Render the create form.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderRegistrationAction()
    {
        try {
            $formCreate = $this->createForm(
                RegistrationForm::class,
                new User()
            );

            return $this->render(
                '@DtwUser/User/registration.html.twig',
                array(
                    'formCreate' => $formCreate->createView()
                )
            );
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                self::ERROR_MESSAGE
            );
            return $this->redirectToRoute('dtw_user_index');
        }
    }

    /**
     * Save in database the user entity.
     *
     * @param Request $request handle the information about the client request.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registeredAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $user = new User();

            $formCreate = $this->createForm(
                RegistrationForm::class,
                $user
            )->handleRequest($request);
            if ($formCreate->isSubmitted() && $formCreate->isValid()) {
                try {
                    $this
                        ->get('manager.user')
                        ->registerUser($user);

                    $this->addFlash(
                        'success',
                        'Successfully registered'
                    );

                    $redirection = $this->redirectToRoute('dtw_user_login');
                } catch (\Exception $e) {
                    $this->addFlash(
                        'error',
                        self::ERROR_MESSAGE
                    );

                    $redirection = $this->redirectToRoute(
                        'dtw_user_registration'
                    );
                }

                return $redirection;
            }

            return $this->render(
                '@DtwUser/User/registration.html.twig',
                array(
                    'formCreate' => $formCreate->createView()
                )
            );
        }

        throw $this->createNotFoundException();
    }
}