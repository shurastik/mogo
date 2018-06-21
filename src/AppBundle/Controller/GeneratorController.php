<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Generator\RandomResultGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GeneratorController
 * @package AppBundle\Controller
 */
class GeneratorController extends Controller
{
    /**
     * @var RandomResultGenerator
     */
    private $generator;

    /**
     * GeneratorController constructor.
     * @param RandomResultGenerator $generator
     */
    public function __construct(RandomResultGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @Route("/{id}/fillDivision/{division}", requirements={"id": "[^/]+"},  methods={"POST"}, name="fill_division")
     * @param string $id
     * @param string $division
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\NoResultException
     */
    public function fillDivisionAction(string $id, string $division): Response
    {
        $this->generator->fillTournamentDivision($id, $division);

        return $this->redirectToRoute('tournament', ['id' => $id]);
    }

    /**
     * @Route("/{id}/fillPlayOff", requirements={"id": "[^/]+"},  methods={"POST"}, name="fill_playoff")
     * @param string $id
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     */
    public function fillPlayOffAction(string $id): Response
    {
        $this->generator->fillTournamentPlayOff($id);

        return $this->redirectToRoute('tournament', ['id' => $id]);
    }
}
