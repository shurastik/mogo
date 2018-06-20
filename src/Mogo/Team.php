<?php
declare(strict_types=1);

namespace Mogo;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Team
 * @package Mogo
 * @ORM\Entity
 * @ORM\Table(name="teams")
 */
class Team
{
    /**
     * @var UuidInterface
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * Team constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
