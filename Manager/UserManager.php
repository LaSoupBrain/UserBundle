<?php

namespace Dtw\UserBundle\Manager;

use Dtw\UserBundle\Entity\User;
use Dtw\UserBundle\Operation\UserOperation;

/**
 * Class UserManager
 *
 * @package Dtw\UserBundle\Manager
 *
 * @author Ali, Muamar
 */
class UserManager
{
    /**
     * @var UserOperation
     */
    private $userOperation;

    /**
     * UserManager constructor.
     *
     * @param UserOperation $userOperation
     *
     * @author Ali, Muamar
     */
    public function __construct(UserOperation $userOperation)
    {
        $this->userOperation = $userOperation;
    }

    /**
     * Creation of user.
     *
     * @param User $user | user to be created.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function create(User $user): UserOperation
    {
        try {
            return $this
                ->userOperation
                ->setUser($user)
                ->createPassword()
                ->create()
                ->save();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at creating of user.'
            );
        }
    }

    /**
     * Create admin
     *
     * @param User $user | user entity to be create.
     * @param string $email inputted email.
     * @param string $password inputted password.
     *
     * @throws \Exception
     * @author Ali Muamar
     *
     * @return UserManager
     */
    public function createAdminDefault(
        User $user,
        string $email,
        string $password
    ): UserManager
    {
        try {
            $this
                ->userOperation
                ->setUser($user)
                ->createDefault(
                    $email,
                    $password
                )
                ->create()
                ->save();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at creating admin default.'
            );
        }

        return $this;
    }

    /**
     * Check if the entered email is already exist in database or not.
     *
     * @param string $email email of the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return bool
     */
    public function isEmailExist(string $email)
    {
        try {
            return $this->userOperation->isEmailExist($email);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at checking if email exist.'
            );
        }
    }

    /**
     * Updating of user.
     *
     * @param User $user | user to be update.
     * @param string $oldName | old user full name.
     * @param string $oldAvatar | old avatar image.
     * @param string $oldHoverAvatar | old hover avatar image.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     *
     * @return UserOperation
     */
    public function update(
        User $user,
        string $oldName,
        string $oldAvatar = null,
        string $oldHoverAvatar = null
    )
    {
        try {
            return $this
                ->userOperation
                ->setUser($user)
                ->updateSlug($oldName)
                ->updateAvatar($oldAvatar)
                ->updateHoverAvatar($oldHoverAvatar)
                ->save();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at updating of user.'
            );
        }
    }

    /**
     * Removing of user.
     *
     * @param User $user | user to be remove.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function delete(User $user): UserOperation
    {
        try {
            return $this->userOperation->remove($user);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at removing of user.'
            );
        }
    }

    /**
     * Getting full name of user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return string
     */
    public function getFullName(): string
    {
        try {
            return $this->userOperation->getFullName();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at getting of full name.'
            );
        }
    }

    /**
     * Getting of user by slug id.
     *
     * @param string $slugId | slug id of the user.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return User|null
     */
    public function getBySlugId(string $slugId): ?User
    {
        try {
            return $this->userOperation->getBySlugId($slugId);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at getting of user by slug id.'
            );
        }
    }

    /**
     * Get by batch of users by specific page.
     *
     * @param int $currentPage | The current page for pagination.
     *
     * @throws \Exception
     * @author Ali Muamar
     *
     * @return array
     */
    public function getByBatch(int $currentPage): array
    {
        try {
            return $this->userOperation->getByBatch($currentPage);
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
     * @author Ali, Muamar
     *
     * @return int
     */
    public function getTotalPages(): int
    {
        try {
            return $this->userOperation->getTotalPages();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at getting the total pages.'
            );
        }
    }

    /**
     * Validate the entered email.
     *
     * @param $email
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserManager
     */
    public function validateEmail($email): UserManager
    {
        try {
            $this->userOperation->validateEmail($email);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at checking if valid email.'
            );
        }

        return $this;
    }

    /**
     * Get the token of the current user.
     *
     * @param string $token
     *
     * @throws \Exception
     *
     * @return User|null
     */
    public function getByToken(string $token): ?User
    {
        try {
            return $this->userOperation->getByToken($token);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the getting token of user.'
            );
        }
    }

    /**
     * Reset password email.
     *
     * @param User $user | user to be reset.
     *
     * @throws \Exception
     *
     * @return UserOperation
     */
    public function resetPasswordEmail(User $user)
    {
        try {
            return $this
                ->userOperation
                ->setUser($user)
                ->setToken()
                ->save()
                ->sendMailResetPassword($user->getEmail(), $user);
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the resetting password.'
            );
        }

    }

    /**
     * Updating the password after sending of reset password.
     *
     * @param User $user | user need to be update password.
     *
     * @throws \Exception
     *
     * @return UserOperation
     */
    public function updatePassword(User $user)
    {
        try {
            return $this
                ->userOperation
                ->setUser($user)
                ->updatePassword()
                ->removeToken()
                ->save();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the updatting of password.'
            );
        }
    }

    /**
     * Create user from registration.
     *
     * @param User $user | user need to be registered.
     *
     * @throws \Exception
     * @author Ali, Muamar
     *
     * @return UserOperation
     */
    public function registerUser(User $user)
    {
        try {
            return $this
                ->userOperation
                ->setUser($user->setWeight(1))
                ->createPassword()
                ->create()
                ->save();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the updatting of password.'
            );
        }
    }
}