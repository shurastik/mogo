<?php
declare(strict_types=1);

namespace Mogo\Dto;

use Mogo\Tournament;

/**
 * Class TournamentDto
 * @package Mogo\Dto
 */
class TournamentDto
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;

    /**
     * @param Tournament $tournament
     * @return TournamentDto
     */
    public static function from(Tournament $tournament): self
    {
        $res = new self();
        $res->id = $tournament->getId()->toString();
        $res->name = (string)$tournament;

        return $res;
    }
}
