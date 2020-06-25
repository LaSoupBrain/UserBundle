<?php

namespace Dtw\UserBundle\Controller;

use Dtw\UserBundle\Entity\User;
use Dtw\UserBundle\Form\RegistrationForm;
use Dtw\UserBundle\Utils\PaginationUtils;
use Dtw\UserBundle\Form\UserEditForm;
use Dtw\UserBundle\Form\UserForm;
use Dtw\UserBundle\Form\UserPasswordForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 *
 * @package Dtw\UserBundle\Controller
 *
 * @author Richard Soliven
 */
class UserController extends Controller
{
    /**
     * Message for error.
     */
    const ERROR_MESSAGE = 'There was an error occurred. Please kindly contact PHP DEVELOPER TEAM';

	/**
	 * Render the user list.
	 *
	 * @author Richard Soliven
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(int $currentPage = PaginationUtils::DEFAULT_PAGE)
	{
		try {
			$userManager = $this->get('manager.user');
			$users = $userManager->getByBatch($currentPage);
			$totalPages = $userManager->getTotalPages();

			return $this->render(
				'@DtwUser/User/list.html.twig',
				array(
					'users' => $users,
					'currentPage' => $currentPage,
					'totalPages' => $totalPages
				)
			);
		} catch (\Exception $e) {
			$this->addFlash(
				'error',
				self::ERROR_MESSAGE
			);
			
			throw new \Exception('Internal Server Error!');
		}
	}

    /**
     * Render the create form.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderCreateAction()
    {
        try {
            $formCreate = $this->createForm(
                UserForm::class,
                new User()
            );

            return $this->render(
                '@DtwUser/User/create.html.twig',
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
    public function addAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $user = new User();

            $formCreate = $this->createForm(
                UserForm::class,
                $user
            )->handleRequest($request);

            if ($formCreate->isSubmitted() && $formCreate->isValid()) {
                try {
                    $this
                        ->get('manager.user')
                        ->setUser($user)
                        ->createPassword()
                        ->create()
                        ->save()
                        ->getUser();

                    $this->addFlash(
                        'success',
                        sprintf('%s sucessfully added.',
                            $user->getEmail()
                        )
                    );

                    $redirection = $this->redirectToRoute('dtw_user_show',
                        array(
                            'slugId' => $user->getSlugId()
                        )
                    );
                } catch (\Exception $e) {
                    $this->addFlash(
                        'error',
                        self::ERROR_MESSAGE
                    );

                    $redirection = $this->redirectToRoute(
                        'dtw_user_create'
                    );
                }

                return $redirection;
            }

            return $this->render(
                '@DtwUser/User/create.html.twig',
                array(
                    'formCreate' => $formCreate->createView()
                )
            );
        }

        throw $this->createNotFoundException();
    }

    /**
     * Render the edition of User.
     *
     * @param string $slugId the slug id of the user.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function renderEditAction($slugId)
    {
        try {
            $user = $this
                ->get('manager.user')
                ->getBySlugId($slugId);

            if(empty($user)) {
                $this->addFlash(
                    'error',
                    'User not exist'
                );
            } else {
                $formEdit = $this->createForm(
                    UserEditForm::class,
                    $user
                );

                $response = $this->render(
                    '@DtwUser/User/update.html.twig',
                    array(
                        'formEdit' => $formEdit->createView(),
                        'user' => $user
                    )
                );
            }
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                self::ERROR_MESSAGE
            );
        }

        return empty($response) ? $this->redirectToRoute('dtw_user_index') : $response;
    }

    /**
     * For updating data in user entity.
     *
     * @param Request $request handle the information about the client request.
     * @param string $slugId the slug id of the user.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $slugId)
    {
        if ($request->getMethod() === 'POST') {
            try {
                $userManager = $this->get('manager.user');

                $user = $this
                    ->get('manager.user')
                    ->getBySlugId($slugId);

                $oldAvatar = $user->getAvatar();
                $oldHoverAvatar = $user->getHoverAvatar();

                if (empty($user->getSlugId())) {
                    $this->addFlash(
                        'error',
                        self::ERROR_MESSAGE
                    );

                    return $this->redirectToRoute('dtw_user_index');
                } else {
                    $formEdit = $this->createForm(
                        UserEditForm::class,
                        $user
                    )->HandleRequest($request);

                    if ($formEdit->isSubmitted() && $formEdit->isValid()) {
                        $user = $userManager
                            ->setUser($formEdit->getData())
                            ->update()
                            ->updateAvatar($oldAvatar)
                            ->updateHoverAvatar($oldHoverAvatar)
                            ->save()
                            ->getUser();

                        $this->addFlash(
                            'success',
                            sprintf('%s sucessfully modified.',
                                $user->getUsername()
                            )
                        );

                        return $this->redirectToRoute(
                            'dtw_user_show',
                            array(
                                'slugId' => $user->getSlugId()
                            )
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    self::ERROR_MESSAGE
                );

                return $this->redirectToRoute('dtw_user_edit',
                    array(
                        'slugId' => $user->getSlugId()
                    ));
            }

            return $this->render(
                '@DtwUser/User/update.html.twig',
                array(
                    'formEdit' => $formEdit->createView()
                )
            );
        }

        throw $this->createNotFoundException();
    }

    /**
     * Render delete page.
     *
     * @param string $slugId the slug id of the user.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderRemoveAction($slugId)
    {
        try {
            $user = $this
                ->get('manager.user')
                ->getBySlugId($slugId);

            if (empty($user)) {
                $this->addFlash(
                    'error',
                    'User not exist'
                );
            } else {
                $response = $this->render(
                    "@DtwUser/User/delete.html.twig",
                    array(
                        'user' => $user
                    )
                );
            }
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                self::ERROR_MESSAGE
            );
        }

        return empty($response) ? $this->redirectToRoute('dtw_user_index') : $response;
    }

    /**
     * Deletes the object (specified through ID) from the "User" Entity.
     *
     * @param string $slugId the slug id of the user.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($slugId)
    {
        try {
            $userManager = $this->get('manager.user');
            $user = $userManager->getBySlugId($slugId);
            if(empty($user)) {
                $this->addFlash(
                    'error',
                    'User not exist'
                );
            } else {
                $userManager
                    ->setUser($user)
                    ->remove();

                $this->addFlash(
                    'success',
                    sprintf('%s sucessfully deleted.',
                        $userManager->getFullName()
                    )
                );
            }
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                self::ERROR_MESSAGE
            );
        }

        return $this->redirectToRoute('dtw_user_index');
    }

    /**
     * To display specific user details.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction($slugId)
    {
        try {
            $user = $this
                ->get('manager.user')
                ->getBySlugId($slugId);

            if(empty($user)) {
                $this->addFlash(
                    'error',
                    'User not exist'
                );
            } else {
                $response =  $this->render(
                    '@DtwUser/User/show.html.twig',
                    array(
                        'user' => $user
                    )
                );
            }
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                self::ERROR_MESSAGE
            );
        }

        return empty($response) ? $this->redirectToRoute('dtw_user_index') : $response;
    }

    /**
     * Render the form of reset password
     *
     * @param string $token the token of the current user.
     *
     * @author Richard Soliven
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editPasswordAction(string $token)
    {
        try {
            $user = $this
                ->get('manager.user')
                ->getByToken($token);
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                self::ERROR_MESSAGE
            );

            return $this->redirectToRoute('login');
        }

        $formEdit = $this->createForm(
            UserPasswordForm::class,
            $user
        );

        return $this->render(
            '@DtwUser/Security/reset_password.html.twig',
            array(
                'formEdit' => $formEdit->createView()
            )
        );
    }

    /**
     * Update the new password of the user
     *
     * @param Request $request handle the information about the client request.
     * @param string $token the token of the current user.
     *
     * @author Muamar Ali
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePasswordAction(Request $request, string $token)
    {
        if ($request->getMethod() === 'POST') {
            try {
                $userManager = $this->get('manager.user');

                $user = $userManager->getByToken($token);
                if (!empty($user->getSlugId())) {
                    $formEdit = $this->createForm(
                        UserPasswordForm::class,
                        $user
                    )->HandleRequest($request);

                    if ($formEdit->isSubmitted() && $formEdit->isValid()) {
                        $userManager
                            ->setUser($formEdit->getData())
                            ->updatePassword()
                            ->removeToken()
                            ->save();

                        return $this->render('@DtwUser/Security/reset_password_success.html.twig');
                    }
                }
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    self::ERROR_MESSAGE
                );
            }

            return $this->render(
                '@DtwUser/Security/reset_password.html.twig',
                array(
                    'formEdit' => $formEdit->createView(),
                    'user' => $user
                )
            );
        }

        throw $this->createNotFoundException();
    }
}