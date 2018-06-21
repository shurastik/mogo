<?php
declare(strict_types=1);

namespace Mogo\Tournament\Match;

/**
 * Interface ResultProvider
 * @package Mogo\Tournament\Match
 */
interface ResultProvider
{
    public function provide(string $matchId): Result;
}
