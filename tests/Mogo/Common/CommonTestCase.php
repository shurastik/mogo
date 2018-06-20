<?php
declare(strict_types=1);

namespace Tests\Mogo\Common;

use Mogo\Team;
use PHPUnit\Framework\TestCase;

/**
 * Class CommonTestCase
 * @package Tests\Mogo\Common
 */
class CommonTestCase extends TestCase
{
    /**
     * @var
     */
    protected $teams = [];

    public function setUp()
    {
        $this->teams = [];
        for ($i = 1 ; $i <= 16; $i++) {
            $this->teams[] = new Team('Team '. $i);
        }
        parent::setUpBeforeClass();
    }
}
