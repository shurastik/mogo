<?php
declare(strict_types=1);

namespace Mogo\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Mogo\Tournament;
use Ramsey\Uuid\UuidInterface;

/**
 * Class TournamentRepository
 * @package Mogo\Repository
 */
class TournamentRepository extends EntityRepository
{
    /**
     * @param UuidInterface $id
     * @return Tournament
     * @throws NoResultException
     */
    public function get(UuidInterface $id): Tournament
    {
         if ($res = $this->find($id)) {
            return $res;
         }
         throw new NoResultException();
    }

    /**
     * @param Tournament $tournament
     * @param bool $flush
     */
    public function save(Tournament $tournament, bool $flush = true): void
    {
        $this->_em->persist($tournament);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
