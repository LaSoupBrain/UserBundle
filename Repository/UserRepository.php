<?php

namespace Dtw\UserBundle\Repository;

use Dtw\UserBundle\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * Return all existing users in the database.
	 *
	 * @author Richard Soliven
	 *
	 * @return array
	 */
	public function getAll(): array
	{
		return $this->findAll();
	}

	/**
	 * Get all active user of the team.
	 *
	 * @param int $teamId the id of the current team.
	 *
	 * @author Richard Soliven
	 *
	 * @return array
	 */
	public function getAllActived(int $teamId): array
	{
		return $this->createQueryBuilder('u')
			->where(
				'u.team = :teamId'
			)
			->setParameters(
				array(
					'teamId' => $teamId
				)
			)
			->getQuery()
			->getResult();
	}

	/**
	 * Return the rows count.
	 *
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @author Richard Soliven
	 *
	 * @return integer
	 */
	public function getRowCount(): int
	{
		return intval(
			$this
				->createQueryBuilder('u')
				->select('COUNT(u)')
				->getQuery()
				->getSingleScalarResult()
		);
	}

	/**
	 * Get the last created tag.
	 *
	 * @author Richard Soliven
	 *
	 * @return mixed
	 */
	public function getLastCreated(): User
	{
		return $this->findOneBy(
			[],
			['createdAt' => 'desc']
		);
	}

	/**
	 * Get the email of user.
	 *
	 * @param string $email the email of the current user.
	 *
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @author Richard Soliven
	 *
	 * @return User
	 */
	public function isEmailExist(string $email): User
	{
		return $this->createQueryBuilder('u')
			->where(
				'u.email = :email'
			)
			->setParameter('email' , $email)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * Get users with limit value.
	 *
	 * @param int $startFrom the start of batch to display.
	 * @param int $limit the limit per batch to display.
	 *
	 * @author Richard
	 *
	 * @return array
	 */
	public function getByBatch(int $startFrom, int $limit): array
	{
		return $this->createQueryBuilder('u')
			->setMaxResults($limit)
			->setFirstResult($startFrom)
			->getQuery()
			->getResult();
	}
}
