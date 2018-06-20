<?php
declare(strict_types=1);

namespace Mogo\Tournament\Match;

use Mogo\Team;

/**
 * Interface ResultProvider
 * @package Mogo\Tournament\Match
 */
interface ResultProvider
{
    public function provide(Team $host, Team $guest): Result;
}
