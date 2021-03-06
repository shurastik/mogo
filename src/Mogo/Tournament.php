<?php
declare(strict_types=1);

namespace Mogo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Mogo\Exception\IllegalStateException;
use Mogo\Exception\InvalidArgument;
use Mogo\Tournament\Match;
use Mogo\Tournament\MatchUpGenerator;
use Mogo\Tournament\PlayOff;
use Mogo\Tournament\TournamentTeam;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Tournament
 * @package Mogo
 * @ORM\Entity(repositoryClass="Mogo\Repository\TournamentRepository")
 * @ORM\Table(name="tournaments")
 */
class Tournament
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
     * @var Collection|Match\RegularMatch[]
     * @ORM\OneToMany(targetEntity="Mogo\Tournament\Match\RegularMatch", mappedBy="tournament", cascade={"PERSIST"})
     */
    private $regularMatches;
    /**
     * Root node of playoff matches binary tree
     *
     * @var Tournament\Match\PlayOffMatch|null
     * @ORM\OneToOne(targetEntity="Mogo\Tournament\Match\PlayOffMatch", inversedBy="tournament", cascade={"PERSIST"})
     */
    private $finalMatch;
    /**
     * @todo Maybe, for more flexible model here should be list with games 3/4, 5/6, ...
     * @var Tournament\Match\PlayOffMatch
     * @ORM\OneToOne(targetEntity="Mogo\Tournament\Match\PlayOffMatch", inversedBy="tournament", cascade={"PERSIST"})
     */
    private $thirdPlaceMatch;
    /**
     * @var Collection|TournamentTeam[]
     * @ORM\OneToMany(targetEntity="Mogo\Tournament\TournamentTeam", mappedBy="tournament", cascade={"PERSIST"})
     */
    private $teams;

    /**
     * Tournament constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        if (empty(\trim($name))) {
            throw new InvalidArgument('Tournament name should not be empty');
        }
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->regularMatches = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->thirdPlaceMatch = new Match\PlayOffMatch($this);
    }

    /**
     * @return array
     */
    public static function getDivisions(): array
    {
        return ['A', 'B'];
    }

    /**
     * @param Team $team
     * @param string $division
     */
    public function addTeam(Team $team, string $division): void
    {
        if ($this->isPlayOffStarted()) {
            throw new IllegalStateException('Sorry, bro, play-off already started');
        }
        $tournamentTeam = new TournamentTeam($division, $team, $this);
        foreach ($this->teams as $hostTeam) {
            if ($hostTeam->getDivision() === $division) {
                $this->regularMatches->add(
                    new Match\RegularMatch($this, new PlayOff\MatchUp($hostTeam, $tournamentTeam))
                );
            }
        }
        $this->teams->add($tournamentTeam);
    }

    /**
     * @param Team $team1
     * @param Team $team2
     * @return Match
     */
    public function getMatch(Team $team1, Team $team2): Match
    {
        foreach ($this->regularMatches as $match) {
            if ($match->containsTeams($team1, $team2)) {
                return $match;
            }
        }
        throw new InvalidArgument('Match not found');
    }

    /**
     * @param UuidInterface $id
     * @return Match
     */
    public function getMatchById(UuidInterface $id): Match
    {
        foreach ($this->regularMatches as $match) {
            if ($match->getId()->equals($id)) {
                return $match;
            }
        }
        if ($this->thirdPlaceMatch->getId()->equals($id)) {
            return $this->thirdPlaceMatch;
        }
        if ($res = $this->finalMatch->findNodeById($id)) {
            return $res;
        }
        throw new InvalidArgument('Match not found');
    }

    /**
     * @return Collection|TournamentTeam[]
     */
    public function getTeams(): Collection
    {
        return new ArrayCollection($this->teams->toArray());
    }

    public function getTournamentTeam(Team $team): TournamentTeam
    {
        return $this->teams
            ->filter(
                function (TournamentTeam $tournamentTeam) use ($team) {
                    return $tournamentTeam->getTeam() === $team;
                }
            )
            ->first();
    }

    /**
     * @return bool
     */
    public function isPlayOffCanBeStarted(): bool
    {
        return $this->allRegularMatchesCompleted() && !$this->isPlayOffStarted();
    }

    /**
     * @param MatchUpGenerator $matchUpGenerator
     */
    public function startPlayoff(MatchUpGenerator $matchUpGenerator): void
    {
        if ($this->isPlayOffStarted()) {
            throw new IllegalStateException('Play-off already started');
        }
        if (!$this->allRegularMatchesCompleted()) {
            throw new IllegalStateException('Not all regular matches completed');
        }
        $matchUps = $matchUpGenerator->create($this->teams);
        $this->finalMatch = Tournament\Match\PlayOffMatch::createTree($this, (int)\log(\count($matchUps) * 2, 2));
        $this->finalMatch->forEachLeaf(
            function (Tournament\Match\PlayOffMatch $node) use (&$matchUps) {
                $node->setMatchUp(\array_shift($matchUps));
            }
        );
    }

    /**
     * @param UuidInterface $id
     * @return Match|null
     */
    public function findPlayOffMatch(UuidInterface $id): ?Match
    {
        if ($this->thirdPlaceMatch->getId()->equals($id)) {
            return $this->thirdPlaceMatch;
        }

        return $this->finalMatch->findNodeById($id);
    }

    /**
     * @return bool
     */
    public function isPlayOffStarted(): bool
    {
        return null !== $this->finalMatch;
    }

    /**
     * @return bool
     */
    public function isPlayOffComplete(): bool
    {
        return $this->isPlayOffStarted() && $this->finalMatch->isCompleted() && $this->thirdPlaceMatch->isCompleted();
    }

    /**
     * @return bool
     */
    public function allRegularMatchesCompleted(): bool
    {
        return 0 === $this->regularMatches
                ->filter(function (Match\RegularMatch $match) { return !$match->isCompleted(); })
                ->count();
    }

    /**
     * @return Match\PlayOffMatch|null
     */
    public function getFinalMatch(): ?Tournament\Match\PlayOffMatch
    {
        return $this->finalMatch;
    }

    public function updatePlayOffMatchUps(): void
    {
        $this->finalMatch->forEachNode(function (Match\PlayOffMatch $match) {
            if ($match->getFirst()) { // already has teams
                return;
            }
            if (!$match->getLeft()->isCompleted()) {
                return;
            }
            if (!$match->getRight()->isCompleted()) {
                return;
            }
            $match->setMatchUp(new PlayOff\MatchUp($match->getLeft()->getWinner(), $match->getRight()->getWinner()));
        });
        if ($this->finalMatch->isCompleted() && !$this->thirdPlaceMatch->isCompleted()) {
            $this->thirdPlaceMatch->setMatchUp(new PlayOff\MatchUp(
                $this->finalMatch->getLeft()->getLoser(),
                $this->finalMatch->getRight()->getLoser()
            ));
        }
    }

    /**
     * @return Match\PlayOffMatch
     */
    public function getThirdPlaceMatch(): Match\PlayOffMatch
    {
        return $this->thirdPlaceMatch;
    }

    /**
     * @return TournamentTeam[]
     */
    public function getFinalRating(): array
    {
        if (!$this->isPlayOffComplete()) {
            throw new IllegalStateException('Not all play-off matches completed');
        }
        $res = [];
        $res[] = $this->finalMatch->getWinner();
        $res[] = $this->finalMatch->getLoser();
        $res[] = $this->thirdPlaceMatch->getWinner();
        $res[] = $this->thirdPlaceMatch->getLoser();
        // sort remaining by totalScore
        foreach ($this->teams->matching(Criteria::create()->orderBy(['totalScore' => Criteria::DESC])) as $team) {
            if (!\in_array($team, $res, true)) {
                continue;
            }
            $res[] = $team;
        }

        return $res;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @param string $division
     * @return Collection|Match\RegularMatch[]
     */
    public function getDivisionMatches(string $division): Collection
    {
        return $this->regularMatches->filter(
            function (Match\RegularMatch $match) use ($division) {
                return $match->getFirst()->getDivision() === $division;
            }
        );
    }
}
