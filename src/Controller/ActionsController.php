<?php

namespace App\Controller;

use App\NextActionsLookup;
use App\Trello\Api;
use App\Trello\ListId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionsController extends AbstractController
{
    /**
     * @Route("/")
     * @Route("/actions")
     *
     * @param Api $trelloApi
     * @return Response
     */
    public function list(Api $trelloApi): Response
    {
        $lookup = new NextActionsLookup($trelloApi, new ListId($_SERVER['TRELLO_NEXT_ACTIONS_LIST_ID']));
        $nextActions = $lookup->lookup();

        return $this->render('actions.html.twig', ['nextActions' => $nextActions]);
    }
}
