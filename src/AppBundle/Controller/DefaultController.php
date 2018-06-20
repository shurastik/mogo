<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use Mogo\TournamentService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @var TournamentService
     */
    private $tournamentService;

    /**
     * DefaultController constructor.
     * @param TournamentService $tournamentService
     */
    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(): Response
    {
        return $this->render('default/index.html.twig', [
            'tournaments' => $this->tournamentService->findAll(),
        ]);
    }

    public function createTournamentAction()
    {
    }

    /**
     * @Route("/{id}", name="tournament", requirements={"id": ".+"}, options={"expose": true})
     * @param string $id
     * @return Response
     */
    public function tournamentAction(string $id): Response
    {
        return $this->render('default/tournament.html.twig', [
            'tournament' => $this->tournamentService->find($id),
        ]);
    }
}
