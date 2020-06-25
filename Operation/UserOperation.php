<?php

namespace Dtw\UserBundle\Operation;

use Doctrine\ORM\EntityManagerInterface;
use Dtw\SlugBundle\Utils\SlugUtils;
use Dtw\StringBundle\Utils\StringUtils;
use Dtw\UserBundle\Entity\User;
use Dtw\UserBundle\Utils\{DatabaseUtils, EmailUtils, PaginationUtils, TokenUtils};
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserOperation
 *
 * @package Dtw\UserBundle\Operation
 *
 * @author Ali, Muamar
 */
class UserOperation
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SlugUtils
     */
    private $slugUtils;

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
     * @var StringUtils
     */
    private $stringUtils;

    /**
     * UserOperation constructor.
     *
     * @param EntityManagerInterface $em
     * @param SlugUtils $slugUtils
     * @param ContainerInterface $container
     * @param DatabaseUtils $databaseUtils
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Filesystem $fileSystem
     * @param EmailUtils $emailUtils
     * @param TokenUtils $tokenUtils
     * @param PaginationUtils $paginationUtils
     *
     * @author Ali Muamar
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
        PaginationUtils $paginationUtils,
        \Dtw\UserBundle\Utils\StringUtils $stringUtils
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
        $this->stringUtils = $stringUtils;
    }

    /**
     * Get the user.
     *
     * @author Ali Muamar
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
     * @return UserOperation
     */
    public function setUser(User $user): UserOperation
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Creation of the user account.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function create(): UserOperation
    {
        try {
            if ($this->user->getRoles() == null) {
                $this->user->setRoles([User::ROLE_ADMIN]);
            }

            $this->generateSlugId();
            $this->generateSlug();
            $this->setAvatar();
            $this->setHoverAvatar();
            $this->user->setUsername($this->user->getEmail());
        } catch (\Exception $e) {
            throw new \Exception(
                'The user can\'t be created, error occured at create.'
            );
        }

        return $this;
    }

    /**
     * Creation for the default super admin user.
     *
     * @param string $email | email of the user.
     * @param string $password | password of the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function createDefault(
        string $email,
        string $password
    ): UserOperation
    {
        try {
            $createdDate = new \DateTime();

            $this->user
                ->setPassword($this->encryptPassword($password))
                ->setEmail($email)
                ->setRoles([User::ROLE_SUPER_ADMIN])
                ->setWeight(1)
                ->setFirstName('super')
                ->setLastName('admin')
                ->setDesignation('Super Admin')
                ->setCreatedAt($createdDate)
                ->setStartedAt($createdDate->modify('-1 day'))
                ->setLocation('Super Admin')
                ->setDescription('Super Admin');
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred while creating default super admin user.'
            );
        }

        return $this;
    }

    /**
     * Encrypting user password.
     *
     * @param string $password password of the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return string
     */
    public function encryptPassword(string $password): string
    {
        try {
            return $this->passwordEncoder->encodePassword(
                $this->user,
                $password
            );
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred while encrypting password.'
            );
        }
    }

    /**
     * Edit a user.
     *
     * @param string $oldName | old user full name.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function updateSlug(string $oldName)
    {
        try {
            if ($oldName != $this->getFullName()) {
                $this->generateSlug();
            }

            $this->user->setUsername($this->user->getEmail());
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the updating of an user.'
            );
        }

        return $this;
    }

    /**
     * Updating the user password.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function updatePassword(): UserOperation
    {
        try {
            $encodedPassword = $this->passwordEncoder->encodePassword(
                $this->user,
                $this->user->getNewPassword()
            );

            $this->user->setPassword($encodedPassword);
        } catch (\Exception $e) {
            throw new \Exception('Password not match to your current password.');
        }

        return $this;
    }

    /**
     * Set password in user entity.
     *
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function setPassword(): UserOperation
    {
        try {
            $encodedPassword = $this->passwordEncoder->encodePassword(
                $this->user,
                $this->user->getPassword()
            );

            $this->user->setPassword($encodedPassword);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the setting of the password.'
            );
        }

        return $this;
    }

    /**
     * Generating the encrypted password.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function createPassword(): UserOperation
    {
        try {
            $userPassword = $this->user->getPassword();

            if (empty($userPassword)) {
                throw new \Exception('Password is empty.');
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
     * Generating the slug name of the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function generateSlug(): UserOperation
    {
        try {
            if (empty($fullName = $this->getFullName($this->user))) {
                throw new \Exception('The slug can\'t be created, name is empty.');
            } else {
                $this
                    ->slugUtils
                    ->checkSlug($fullName, $this->user, User::class);
            }
        } catch (\Exception $e) {
            throw new \Exception('An error occurred at generating slug.');
        }

        return $this;
    }

    /**
     * Generating the slug id of the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function generateSlugId(): UserOperation
    {
        try {
            $userId = $this->entityCount($this->user);

            if ($userId <= 0) {
                $userId++;

                $this->user->setSlugId(
                    $this
                        ->slugUtils
                        ->slugifyId($userId, User::SLUG_ID_PREFIX)
                );
            } else {
                $result = ltrim(
                    $this->getLastCreated()->getSlugId(),
                    User::SLUG_ID_PREFIX
                );

                $deSlugId = intval(ltrim($result, '0'));
                $deSlugId++;

                $this->user->setSlugId(
                    $this
                        ->slugUtils
                        ->slugifyId($deSlugId, User::SLUG_ID_PREFIX)
                );
            }
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at generating the slug id'
            );
        }

        return $this;
    }

    /**
     * Counts the rows.
     *
     * @throws \Exception
     * @author Ali, Muamar
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
                'An error occurred while retrieving the row count.'
            );
        }
    }

    /**
     * Get the last created tag.
     *
     * @throws \Exception
     * @author Ali, Muamar
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
                'An error occurred while getting the last created user.'
            );
        }
    }

    /**
     * Uploads the hover avatar of a user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function setAvatar(): UserOperation
    {
        try {
            if (($avatar = $this->user->getAvatar()) instanceof UploadedFile) {
                $avatarFileName = sprintf('%s.%s', md5(uniqid()), $avatar->guessExtension());
                $avatar->move(
                    $this->container->getParameter('user_directory'),
                    $avatarFileName
                );

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
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function setHoverAvatar(): UserOperation
    {
        try {
            if (($avatarHover = $this->user->getHoverAvatar()) instanceof UploadedFile) {
                $hoverAvatarFileName = sprintf('%s.%s', md5(uniqid()), $avatarHover->guessExtension());
                $avatarHover->move(
                    $this->container->getParameter('user-hover_directory'),
                    $hoverAvatarFileName
                );

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
     * Update the Avatar.
     *
     * @param string|null $oldAvatar
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function updateAvatar(string $oldAvatar = null)
    {
        try {
            if ($this->user->getAvatar() === null) {
                $this->user->setAvatar($oldAvatar);
            } else {
                if (!empty($oldAvatar)) {
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
     * @return UserOperation
     */
    public function updateHoverAvatar(string $oldHoverAvatar = null)
    {
        try {
            if ($this->user->getHoverAvatar() === null) {
                $this->user->setHoverAvatar($oldHoverAvatar);
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
     * Delete a image of avatar.
     *
     * @param string $oldAvatar this is the old avatar.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return mixed
     */
    public function deleteAvatar(string $oldAvatar): UserOperation
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
     * @author Ali, Muamar
     *
     * @return mixed
     */
    public function deleteHoverAvatar(string $oldHoverAvatar): UserOperation
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
     * Gets and concatenates the First name, middle name, and last name with spaces.
     *
     * @author Ali, Muamar
     *
     * @return string
     */
    public function getFullName(): string
    {
        try {
            return sprintf('%s %s %s',
                $this->user->getFirstName(),
                $this->user->getMiddleName(),
                $this->user->getLastName()
            );
        } catch (\Exception $e ) {
            throw new \Exception(
                'An error occurred at getting user full name.'
            );
        }
    }

    /**
     * Get the token of the current user.
     *
     * @param string $token is for the reset password.
     *
     * @throws \Exception
     * @author Ali Muamar
     *
     * @return User|null
     */
    public function getByToken(string $token): ?User
    {
        try {
            $user = $this
                ->em
                ->getRepository(User::class)
                ->findOneBy(['token' => $token]);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the getting token of user.'
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
     * @author Ali Muamar
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
     * Get the user by the slug id.
     *
     * @param string $slugId The slugId of the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return User
     */
    public function getBySlugId(string $slugId): ?User
    {
        try {
            $user = $this
                ->em
                ->getRepository(User::class)
                ->findOneBy(['slugId' => $slugId]);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the getting the slug id of an user.'
            );
        }

        return $user;
    }

    /**
     * Return all the existing user from the database.
     *
     * @throws \Exception
     * @author Ali, Muamar
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
     * @author Ali, Muamar
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
                'An error occurred at the fetching all actived users in the database.'
            );
        }
    }

    /**
     * Return all the existing email of user from the database.
     *
     * @param string $email | inputted email
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return User
     */
    public function isEmailExist(string $email)
    {
        try {
            return $this
                ->em
                ->getRepository(User::class)
                ->isEmailExist($email);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set the new token for the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function setToken(): UserOperation
    {
        // @Todo When we wil addd the class beetwen controller and manager, pass as parameter the token to this function
        try {
            $this->user->setToken(
                $this
                    ->tokenUtils
                    ->generateToken()
            );
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at generating token.'
            );
        }

        return $this;
    }

    /**
     * Remove the token of the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function removeToken(): UserOperation
    {
        try {
            $this->user->setToken("");
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at removing token.'
            );
        }
        return $this;
    }

    /**
     * Sending of email to the users email
     *
     * @param string $email the email of current user
     * @param User $user the current user
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function sendMailResetPassword(
        string $email,
        User $user
    ): UserOperation
    {
        try {
            $this->emailUtils->resetPassword($email, $user);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at sending email for reseting of password.'
            );
        }

        return $this;
    }

    /**
     * Get the total pages.
     *
     * @throws \Exception
     * @author Ali, Muamar
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

    /**
     * Validate the pass value email.
     *
     * @param $emailQuestion
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function validateEmail($emailQuestion): UserOperation
    {
        try {
            $this->stringUtils->validateEmail($emailQuestion);
        } catch (\Exception $e ) {
            throw new \Exception(
                'An error occurred at checking if valid email.'
            );
        }

        return $this;
    }

    /**
     * Save a user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function save(): UserOperation
    {
        try {
            $this->databaseUtils->save($this->user);
        } catch (\Exception $e ) {
            throw new \Exception(
                'An error occurred at saving.'
            );
        }

        return $this;
    }

    /**
     * Delete a user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function remove($user): UserOperation
    {
        try {
            $this->databaseUtils->remove($user);
        } catch (\Exception $e ) {
            throw new \Exception(
                'An error occurred at removing.'
            );
        }

        return $this;
    }
}