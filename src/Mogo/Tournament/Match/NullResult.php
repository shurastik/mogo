<?php
declare(strict_types=1);

namespace Mogo\Tournament\Match;

/**
 * Class NullResult
 * @package Mogo\Tournament\Match
 */
class NullResult extends Result
{
    /**
     * NullResult constructor.
     */
    public function __construct()
    {
        $this->firstScore = null;
        $this->secondScore = null;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return true;
    }
}
