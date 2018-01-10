<?php

namespace App\Controller;

use App\NextActionForProjectLookup;
use App\NextActionsLookup;
use App\Trello\Api;
use App\Trello\Config;
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
     * @param Config $config
     * @return Response
     */
    public function list(Api $trelloApi, Config $config): Response
    {
        $lookup = new NextActionsLookup(
            $trelloApi,
            new NextActionForProjectLookup($trelloApi),
            $config
        );
        $nextActions = $lookup->lookup();

        return $this->render('actions.html.twig', ['nextActions' => $nextActions]);
    }
}
