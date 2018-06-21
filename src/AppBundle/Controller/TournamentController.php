<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Form\CreateTournamentType;
use Doctrine\ORM\NoResultException;
use Mogo\Dto\CreateTournamentCommand;
use Mogo\Tournament\Match\Result;
use Mogo\TournamentService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TournamentController
 * @package AppBundle\Controller
 */
class TournamentController extends Controller
{
    /**
     * @var TournamentService
     */
    private $tournamentService;

    /**
     * TournamentController constructor.
     * @param TournamentService $tournamentService
     */
    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction(Request $request): Response
    {
        $form = $this->createForm(CreateTournamentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateTournamentCommand $command */
            $command = $form->getData();
            $tournament = $this->tournamentService->create($command);
            return $this->redirectToRoute('tournament', ['id' => $tournament->id]);
        }

        return $this->render('default/index.html.twig', [
            'tournaments' => $this->tournamentService->findAll(),
            'createTournamentFrom' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tournament", requirements={"id": "[^/]+"}, options={"expose": true})
     * @param string $id
     * @return Response
     */
    public function tournamentAction(string $id): Response
    {
        try {
            return $this->render('default/tournament.html.twig', [
                'page' => $this->tournamentService->find($id),
            ]);
        } catch (NoResultException $ex) {
            throw new NotFoundHttpException(\sprintf('Tournament "%s" not found', $id));
        }
    }

    /**
     * @Route("/{id}/match/{matchId}", requirements={"POST"}, requirements={"id": "[^/]+", "matchId": "[^/]+"}, name="finish_match")
     * @param string $id
     * @param string $matchId
     * @param Request $request
     * @return Response
     * @throws NoResultException
     */
    public function matchResultAction(string $id, string $matchId, Request $request): Response
    {
        $this->tournamentService->finishMatch(
            $id,
            $matchId,
            new Result($request->request->getInt('firstScore'), $request->request->getInt('secondScore'))
        );

        return $this->redirectToRoute('tournament', ['id' => $id]);
    }
}
