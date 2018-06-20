<?php
declare(strict_types=1);

namespace Mogo\Exception;

use Mogo\Exception;
use Mogo\Team;

/**
 * Class TeamNotFoundException
 * @package Mogo\Exception
 */
class TeamNotFoundException extends Exception
{
    /**
     * @var Team
     */
    private $team;

    /**
     * TeamNotFoundException constructor.
     * @param Team $team
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
        parent::__construct(\sprintf('Team "%s" not found', $team));
    }
}
