<?php
declare(strict_types=1);

namespace Mogo\Tournament\Match;

use Doctrine\ORM\Mapping as ORM;
use Mogo\Tournament;
use Mogo\Tournament\Match;
use Mogo\Tournament\PlayOff\MatchUp;
use Ramsey\Uuid\UuidInterface;

/**
 * Class PlayoffMatch
 * @package Mogo\Tournament\Match
 * @ORM\Entity()
 */
class PlayOffMatch extends Match
{
    /**
     * @var PlayOffMatch
     * @ORM\OneToOne(targetEntity="Mogo\Tournament\Match\PlayOffMatch", cascade={"PERSIST"})
     */
    private $left;
    /**
     * @var PlayOffMatch
     * @ORM\OneToOne(targetEntity="Mogo\Tournament\Match\PlayOffMatch", cascade={"PERSIST"})
     */
    private $right;

    /**
     * @inheritdoc
     */
    public function complete(Result $result): void
    {
        parent::complete($result);
        $this->tournament->updatePlayOffMatchUps();
    }

    /**
     * @return PlayOffMatch
     */
    public function getLeft(): PlayOffMatch
    {
        return $this->left;
    }

    /**
     * @param PlayOffMatch $left
     */
    public function setLeft(PlayOffMatch $left): void
    {
        $this->left = $left;
    }

    /**
     * @return PlayOffMatch
     */
    public function getRight(): PlayOffMatch
    {
        return $this->right;
    }

    /**
     * @param PlayOffMatch $right
     */
    public function setRight(PlayOffMatch $right): void
    {
        $this->right = $right;
    }

    /**
     * @return bool
     */
    public function isLeaf(): bool
    {
        return null === $this->left && null === $this->right;
    }

    /**
     * @param MatchUp $matchUp
     */
    public function setMatchUp(MatchUp $matchUp): void
    {
        $this->first = $matchUp->getFirst();
        $this->second = $matchUp->getSecond();
    }

    /**
     * @param Tournament $tournament
     * @param int $level
     * @return PlayOffMatch
     */
    public static function createTree(Tournament $tournament, int $level): PlayOffMatch
    {
        $match = new self($tournament);
        if ($level > 1) {
            $match->setLeft(self::createTree($tournament, $level - 1));
            $match->setRight(self::createTree($tournament, $level - 1));
        }

        return $match;
    }

    /**
     * @param callable $consumer
     */
    public function forEachLeaf(callable $consumer): void
    {
        $this->forEachNode(function (PlayOffMatch $node) use ($consumer) {
            if ($node->isLeaf()) {
                $consumer($node);
            }
        });
    }

    /**
     * @param callable $consumer
     */
    public function forEachNode(callable $consumer): void
    {
        $consumer($this);
        if (!$this->isLeaf()) {
            $this->left->forEachNode($consumer);
            $this->right->forEachNode($consumer);
        }
    }

    /**
     * @param UuidInterface $id
     * @return PlayOffMatch|null
     */
    public function findNodeById(UuidInterface $id): ?self
    {
        if ($this->id->equals($id)) {
            return $this;
        }
        if (null !== ($found = $this->left->findNodeById($id))) {
            return $found;
        }

        return $this->right->findNodeById($id);
    }
}
