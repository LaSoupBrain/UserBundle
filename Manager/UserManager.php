<?php

namespace Dtw\UserBundle\Manager;

use Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\FileSystemCacheTest;
use Doctrine\ORM\EntityManagerInterface;
use Dtw\UserBundle\Utils\DatabaseUtils;
use Dtw\UserBundle\Utils\EmailUtils;
use Dtw\UserBundle\Utils\SlugUtils;
use Dtw\UserBundle\Utils\TokenUtils;
use Dtw\UserBundle\Utils\PaginationUtils;
use Dtw\UserBundle\Entity\User;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class UserManager
 *
 * @package Dtw\UserBundle\Manager
 *
 * @author Richard Soliven
 */
class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SlugUtils
     */
    private $slugUtils;

    /**
     * @var User
     */
    private $user;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var DatabaseUtils
     */
    private $databaseUtils;

    /**
     * @var TokenUtils
     */
    private $tokenUtils;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var EmailUtils
     */
    private $emailUtils;

	/**
	 * @var PaginationUtils
	 */
	private $paginationUtils;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface $em
     * @param SlugUtils $slugUtils
     * @param ContainerInterface $container
     * @param DatabaseUtils $databaseUtils
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Filesystem $fileSystem
     * @param EmailUtils $emailUtils
     * @param TokenUtils $tokenUtils
	 * @param \DtwCoreBundle\Utils\PaginationUtils $paginationUtils
     */
    public function __construct(
        EntityManagerInterface $em,
        SlugUtils $slugUtils,
        ContainerInterface $container,
        DatabaseUtils $databaseUtils,
        UserPasswordEncoderInterface $passwordEncoder,
        Filesystem $fileSystem,
        EmailUtils $emailUtils,
        TokenUtils $tokenUtils,
		PaginationUtils $paginationUtils
    ) {
        $this->em = $em;
        $this->slugUtils = $slugUtils;
        $this->container = $container;
        $this->databaseUtils = $databaseUtils;
        $this->passwordEncoder = $passwordEncoder;
        $this->emailUtils = $emailUtils;
        $this->fileSystem = $fileSystem;
        $this->tokenUtils = $tokenUtils;
		$this->paginationUtils = $paginationUtils;
    }

    /**
     * Get the user.
     *
     * @author Richard Soliven
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set the user.
     *
     * @param User $user
     *
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function setUser(User $user): UserManager
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Return all the existing user from the database.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return mixed
     */
    public function getAll()
    {
        try {
            return $this
                ->em
                ->getRepository(User::class)
                ->getAll();

        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the fetching all users in the database.'
            );
        }
    }

    /**
     * Get all the active user of the team.
     *
     * @param int $teamId the id of the current team.
     *
     * @throws \Exception
     * @author  Richard Soliven
     *
     * @return array
     */
    public function getAllActived(int $teamId): array
    {
        try {
            return $this
                ->em
                ->getRepository(User::class)
                ->getAllActived($teamId);

        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the fetching all users in the database.'
            );
        }
    }

    /**
     * Return all the existing email of user from the database.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return mixed
     */
    public function isEmailExist($email): User
    {
        try {
            return $this
                ->em
                ->getRepository(User::class)
                ->isEmailExist($email);

        } catch (\NoResultException $e) {
            return null;
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the fetching all users in the database.'
            );
        }
    }

    /**
     * Creation of the user account.
     *
     * @throws \Exception
     *
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function create(): UserManager
    {
        try {
            if ($this->user->getRoles() == null) {
                $this->user->setRoles(array(User::ROLE_ADMIN));
            }
            $this->generateSlugId();
            $this->generateSlug();
            $this->setAvatar();
            $this->setHoverAvatar();
            $this->user->setUsername($this->user->getEmail());
        } catch (\Exception $e) {
            throw new \Exception(
                'The worker can\t be created, no name in the user. the slug can\'t be generated'
            );
        }

        return $this;
    }

    /**
     * Uploads the hover avatar of a user.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function setAvatar(): UserManager
    {
        try {
            if (($avatar = $this->user->getAvatar()) instanceof UploadedFile) {
                $avatarFileName = md5(uniqid()) . '.' . $avatar->guessExtension();
                $avatar->move(
                    $this->container->getParameter('user_directory'),
                    $avatarFileName);

                $this->user->setAvatar($avatarFileName);
            }
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred while uploading the user avatar.'
            );
        }

        return $this;
    }

    /**
     * Uploads the hover avatar of a user.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function setHoverAvatar(): UserManager
    {
        try {
            if (($avatarHover = $this->user->getHoverAvatar()) instanceof UploadedFile) {
                $hoverAvatarFileName = md5(uniqid()) . '.' . $avatarHover->guessExtension();
                $avatarHover->move(
                    $this->container->getParameter('user-hover_directory'),
                    $hoverAvatarFileName);

                $this->user->setHoverAvatar($hoverAvatarFileName);
            }
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred while uploading hover avatar of a user.'
            );
        }

        return $this;
    }

    /**
     * Generating the slug id of the user.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function generateSlugId(): UserManager
    {
        $userId = $this->entityCount($this->user);

        try {
            if ($userId <= 0) {
                $userId++;

                $this
                    ->user
                    ->setSlugId(
                        $this
                            ->slugUtils
                            ->slugifyId($userId, 'u')
                    );
            } else {
                $result = ltrim(
                    $this
                        ->getLastCreated()
                        ->getSlugId(),
                    'u'
                );
                $deSlugId = intval(ltrim($result, '0'));

                $deSlugId++;

                $this
                    ->user
                    ->setSlugId(
                        $this
                            ->slugUtils
                            ->slugifyId($deSlugId, 'u')
                    );
            }
        } catch (\Exception $e) {
            throw new \Exception(
                'There\s an error in creating the slug id'
            );
        }

        return $this;
    }

    /**
     * Counts the rows.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return int
     */
    public function entityCount(): int
    {
        try {
            return $this
                ->em
                ->getRepository(User::class)
                ->getRowCount();
        } catch (\Exception $e) {
            throw new \Exception(
                'Error occurred while retrieving the row count.'
            );
        }
    }

    /**
     * Get the last created tag.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return mixed
     */
    public function getLastCreated()
    {
        try {
            return $this
                ->em
                ->getRepository(User::class)
                ->getLastCreated();
        } catch (\Exception $e) {
            throw new \Exception(
                'Error occurred while getting the last created user.'
            );
        }
    }

    /**
     * Generating the slug name of the user.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function generateSlug(): UserManager
    {
        $fullName = $this->getFullName($this->user);

        if (empty($fullName)) {
            throw new \Exception('The slug can\t be created, no Name found.');
        } else {
            try {
                $this
                    ->user
                    ->setSlug(
                        $this
                            ->slugUtils
                            ->slugify($fullName)
                    );
            } catch (\Exception $e) {
                throw new \Exception('There\s an error in creating the slug.');
            }
        }

        return $this;
    }

    /**
     * Gets and concatenates the First name, middle name, and last name with spaces.
     *
     * @author Richard Soliven
     *
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s %s %s',
            $this->user->getFirstName(),
            $this->user->getMiddleName(),
            $this->user->getLastName()
        );
    }

    /**
     * Update the Avatar.
     *
     * @param string|null $oldAvatar
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function updateAvatar(string $oldAvatar = null): UserManager
    {
        try {
            if ($this->user->getAvatar() === null) {
                $this->user->setAvatar($oldAvatar);
            } else {
                if(!empty($oldAvatar)) {
                    $this->deleteAvatar($oldAvatar);
                }
                $this->setAvatar();
            }
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the editing avatar of an user.'
            );
        }

        return $this;
    }

    /**
     * Update the HoverAvatar.
     *
     * @param string $oldHoverAvatar
     *
     * @throws \Exception
     * @auhtor Richard Soliven
     *
     * @return UserManager
     */
    public function updateHoverAvatar(string $oldHoverAvatar = null): UserManager
    {
        try {
            if ($this->user->getHoverAvatar() === null) {
                $this
                    ->user
                    ->setHoverAvatar($oldHoverAvatar);
            } else {
                if(!empty($oldHoverAvatar)) {
                    $this->deleteHoverAvatar($oldHoverAvatar);
                }
                $this->setHoverAvatar();
            }
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the editing hover avatar of an user.'
            );
        }

        return $this;
    }

    /**
     * Generating the encrypted password.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function createPassword(): UserManager
    {
        try {
            $userPassword = $this->user->getPassword();
            if (empty($userPassword)) {
                throw new \Exception('Password field is null.');
            } else {
                $password = $this->passwordEncoder->encodePassword($this->user, $userPassword, null);

                $this->user->setPassword($password);
            }
        } catch (\Exception $e) {
            throw new \Exception(
                'Password field must have value.'
            );
        }

        return $this;
    }

    /**
     * Edit a user.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function update(): UserManager
    {
        try {
            $this->generateSlug();
            $this->user->setUsername($this->user->getEmail());
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the creation of an user.'
            );
        }

        return $this;
    }

    /**
     * Editing the user password.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function updatePassword(): UserManager
    {
        try {
            $encodedPassword = $this->passwordEncoder->encodePassword(
                $this->user,
                $this
                    ->user
                    ->getNewPassword()
            );

            $this
                ->user
                ->setPassword($encodedPassword);
        } catch (\Exception $e) {
            throw new \Exception('Password not match to your current password.');
        }

        return $this;
    }

    /**
     * Set password in user entity.
     *
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function setPassword(): UserManager
    {
        try {
            $encodedPassword = $this->passwordEncoder->encodePassword(
                $this->user,
                $this
                    ->user
                    ->getPassword()
            );

            $this
                ->user
                ->setPassword($encodedPassword);
        } catch (\Exception $e) {
            throw new $e;
        }

        return $this;
    }

    /**
     * Get the user by the slug id.
     *
     * @param string $slugId The slugId of the user.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return User
     */
    public function getBySlugId(string $slugId): ?User
    {
        try {
            $user = $this
                ->em
                ->getRepository(User::class)
                ->findOneBy(
                    array(
                        'slugId' => $slugId
                    )
                );
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the getting the slug id of an user.'
            );
        }

        return $user;
    }

    /**
     * Delete a image of avatar.
     *
     * @param string $oldAvatar this is the old avatar.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return mixed
     */
    public function deleteAvatar(string $oldAvatar): UserManager
    {
        try {
            $this->fileSystem->remove(
                $this->container->getParameter('user_directory')
                . DIRECTORY_SEPARATOR
                . $oldAvatar
            );
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the deletion of avatar.'
            );
        }

        return $this;
    }

    /**
     * Delete a image of hover avatar.
     *
     * @param string $oldHoverAvatar this is the old hover avatar.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return mixed
     */
    public function deleteHoverAvatar(string $oldHoverAvatar): UserManager
    {
        try {
            $this->fileSystem->remove(
                $this->container->getParameter('user-hover_directory')
                . DIRECTORY_SEPARATOR
                . $oldHoverAvatar
            );
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the deletion of hover avatar.'
            );
        }

        return $this;
    }

    /**
     * Remove the token of the user.
     *
     * @throws \Exception
     * @author  Richard Soliven
     *
     * @return UserManager
     */
    public function removeToken(): UserManager
    {
        try {
            $this->user->setToken("");
        } catch (\Exception $e) {
            throw new \Exception(
                'Can\'t generate token.'
            );
        }
        return $this;
    }


    /**
     * Set the new token for the user.
     *
     * @throws \Exception
     * @author  Richard Soliven
     *
     * @return UserManager
     */
    public function setToken(): UserManager
    {
        // @Todo When we wil addd the class beetwen controller and manager, pass as parameter the token to this function
        try {
            $this
                ->user
                ->setToken(
                    $this
                        ->tokenUtils
                        ->generateToken()
                );
        } catch (\Exception $e) {
            echo $e->getMessage();die();
            throw new \Exception(
                'Can\'t generate token.'
            );
        }

        return $this;
    }

    /**
     * Delete a user.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function remove(): UserManager
    {
        $this
            ->databaseUtils
            ->remove($this->user);

        return $this;
    }

    /**
     * Save a user.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return UserManager
     */
    public function save(): UserManager
    {
        $this
            ->databaseUtils
            ->save($this->user);

        return $this;
    }

    /**
     * Sending of email to the users email
     *
     * @param string $email the email of current user
     * @param User $user the current user
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return User
     */
    public function sendMailResetPassword(string $email, User $user): UserManager
    {
        try {
            $this
                ->emailUtils
                ->resetPassword($email, $user);
        } catch (\Exception $e) {
            throw new \Exception(
                'Can\'t send email.'
            );
        }

        return $this;
    }

    /**
     * Get the token of the current user.
     *
     * @param string $token is for the reset password.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return User
     */
    public function getByToken(string $token): User
    {
        try {
            $user = $this
                ->em
                ->getRepository(User::class)
                ->findOneBy(
                    array(
                        'token' => $token
                    )
                );
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the getting the slug id of an user.'
            );
        }

        return $user;
    }

	/**
	 * Get by batch of users by specific page.
	 *
	 * @param int $currentPage The current page for pagination.
	 *
	 * @throws \Exception
	 * @author Richard
	 *
	 * @return array
	 */
	public function getByBatch(int $currentPage): array
	{
		try {
			return $this
				->em
				->getRepository(User::class)
				->getByBatch(
					$this
						->paginationUtils
						->batchStartFrom($currentPage),
					PaginationUtils::BATCH_LIMIT
				);
		} catch (\Exception $e) {
			throw new \Exception(
				'An error occurred at fetching users by page.'
			);
		}
	}

	/**
	 * Get the total pages.
	 *
	 * @throws \Exception
	 * @author Richard
	 *
	 * @return int
	 */
	public function getTotalPages(): int
	{
		try {
			return $this->paginationUtils->getTotalPages(count($this->getAll()));
		} catch (\Exception $e) {
			throw new \Exception(
				'An error occurred at getting the total pages.'
			);
		}
	}
}