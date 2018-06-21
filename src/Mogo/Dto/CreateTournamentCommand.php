<?php
declare(strict_types=1);

namespace Mogo\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateTournamentCommand
 * @package Mogo\Dto
 */
class CreateTournamentCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="50")
     * @var string
     */
    public $name;
}
