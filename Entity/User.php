<?php

namespace Dtw\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Dtw\UserBundle\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already registered."
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="This username is already registered."
 * )
 */
class User implements UserInterface, \Serializable
{

    /**
     * To set an user to admin role.
     */
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Error message for image validation.
     */
    const IMAGE_MIME_ERROR_MESSAGE = 'Invalid file type. Please upload a valid file type (.jpeg) or (.png).';

    /**
     * The Jpeg Mime type.
     */
    const MIME_TYPE_JPEG = 'image/jpeg';

    /**
     * The PNG Mime type.
     */
    const MIME_TYPE_PNG = 'image/png';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * For holding the current password in updating password
     */
    private $oldPassword;

    /**
     * For holding the new password in updating password
     */
    private $newPassword;

    /**
     * * @Assert\Length(
     *     max=50,
     *     maxMessage = "Your email must be 50 characters only"
     * )
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer")
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="text", nullable=true)
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="hoverAvatar", type="text", nullable=true)
     */
    private $hoverAvatar;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage = "Your first name must be 50 characters only"
     * )
     * @Assert\NotBlank(
     *     message="Mandatory"
     * )
     * @Assert\Regex("/^[-a-zA-ZÑñ0-9~. \/ ]+$/",
     *
     *     message="Field contains invalid characters."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage = "Your middle name must be 50 characters only"
     * )
     * @Assert\Regex("/^[-a-zA-ZÑñ0-9~. \/ ]+$/",
     *
     *     message="Field contains invalid characters."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
     */
    private $middleName;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage = "Your last name must 50 characters only."
     * )
     * @Assert\NotBlank(
     *     message="Mandatory"
     * )
     *
     * @Assert\Regex("/^[-a-zA-ZÑñ0-9~. \/ ]+$/",
     *
     *     message="Field contains invalid characters."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage = "Your designation must be 50 characters only."
     * )
     * @Assert\NotBlank(
     *     message="Mandatory"
     * )
     *
     * @Assert\Regex("/^[a-zA-Z0-9~\/ ]+$/",
     *
     *     message="Field contains invalid characters."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @Assert\NotBlank(
     *     message="Mandatory"
     * )
     * @Assert\LessThan("today")
     *
     * @var \DateTime
     *
     * @ORM\Column(name="startedAt", type="date", nullable=true)
     */
    private $startedAt;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage = "Your location must be 50 characters only."
     * )
     * @Assert\NotBlank(
     *     message="Mandatory"
     * )
     * @Assert\Regex("/^[a-zA-Z0-9~\/ ]+$/",
     *
     *     message="Field contains invalid characters."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage = "Your skype must be 50 characters only."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="skype", type="string", length=255, nullable=true)
     */
    private $skype;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage = "Your slack must be 50 characters only."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="slack", type="string", length=255, nullable=true)
     */
    private $slack;

    /**
     * @Assert\Length(
     *     max=700,
     *     maxMessage = "The description must be 500 characters only."
     * )
     * @Assert\NotBlank(
     *     message="Mandatory"
     * )
     *
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true, length=700)
     */
    private $description;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=180, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="slugId", type="string", length=180, unique=true, nullable=true)
     */
    private $slugId;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->isActive = true;
    }

    /**
     * get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * set username.
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * set password.
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param mixed $oldPassword
     * @return User
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     * @return User
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    /**
     * get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * set email.
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * get isActive.
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * get salt.
     *
     * @return null|string
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * get roles.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;
        return $roles;
    }

    /**
     * set roles.
     *
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * Set weight.
     *
     * @param integer $weight
     *
     * @return User
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight.
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set hoverAvatar.
     *
     * @param string $hoverAvatar
     *
     * @return User
     */
    public function setHoverAvatar($hoverAvatar)
    {
        $this->hoverAvatar = $hoverAvatar;

        return $this;
    }

    /**
     * Get hoverAvatar.
     *
     * @return string
     */
    public function getHoverAvatar()
    {
        return $this->hoverAvatar;
    }

    /**
     * Set Avatar.
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set firstName.
     *
     * @param string string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set middleName
     *
     * @param string middleName
     *
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return User
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     *
     * @return User
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return User
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set skype
     *
     * @param string $skype
     *
     * @return User
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Get skype
     *
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * Set slack
     *
     * @param string $slack
     *
     * @return User
     */
    public function setSlack($slack)
    {
        $this->slack = $slack;

        return $this;
    }

    /**
     * Get slack
     *
     * @return string
     */
    public function getSlack()
    {
        return $this->slack;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * set isActive
     *
     * @param string $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return User
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slugId
     *
     * @param string $slugId
     *
     * @return User
     */
    public function setSlugId ($slugId)
    {
        $this->slugId = $slugId;

        return $this;
    }

    /**
     * Get slugId
     *
     * @return string
     */
    public function getSlugId()
    {
        return $this->slugId;
    }
}

