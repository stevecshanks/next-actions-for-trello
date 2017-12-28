<?php

namespace App\Controller;

use App\Trello\Api;
use App\Trello\Card;
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
    public function list(Api $trelloApi)
    {
        /** @var Card[] $cards */
        $cards = array_merge(
            $trelloApi->fetchCardsIAmAMemberOf(),
            $trelloApi->fetchCardsOnList(new ListId($_SERVER['TRELLO_NEXT_ACTIONS_LIST_ID']))
        );

        $listElements = "";
        foreach ($cards as $card) {
            $listElements .= '<li>' . htmlspecialchars($card->getName()) . '</li>';
        }

        return $this->render('actions.html.twig', ['cards' => $cards]);
    }
}
