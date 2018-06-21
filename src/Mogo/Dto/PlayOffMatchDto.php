<?php
declare(strict_types=1);

namespace Mogo\Dto;

use Mogo\Tournament\Match\PlayOffMatch;

/**
 * Class PlayOffMatchDto
 * @package Mogo\Dto
 */
class PlayOffMatchDto
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var PlayOffMatchDto|null
     */
    public $left;
    /**
     * @var PlayOffMatchDto|null
     */
    public $right;
    /**
     * @var MatchDto
     */
    public $match;

    /**
     * @param PlayOffMatch|null $match
     * @return PlayOffMatchDto
     */
    public static function from(?PlayOffMatch $match): ?self
    {
        if (null === $match) {
            return null;
        }
        $res = new self();
        $res->id = $match->getId()->toString();
        $res->match = MatchDto::from($match);
        $res->left = self::from($match->getLeft());
        $res->right = self::from($match->getRight());

        return $res;
    }

    /**
     * @param PlayOffMatchDto $matchDto
     * @param int $level
     * @param array $result
     */
    public static function splitTree(PlayOffMatchDto $matchDto, int $level, array &$result): void
    {
        $result[$level][] = $matchDto;
        if ($matchDto->left) {
            self::splitTree($matchDto->left, $level + 1, $result);
        }
        if ($matchDto->right) {
            self::splitTree($matchDto->right, $level + 1, $result);
        }
    }
}