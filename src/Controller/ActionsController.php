<?php

namespace App\Controller;

use App\Trello\Api;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionsController
{
    /**
     * @Route("/actions")
     *
     * @return Response
     */
    public function list(Api $trelloApi)
    {
        $cards = $trelloApi->fetchCardsIAmAMemberOf();

        $listElements = "";
        foreach ($cards as $card) {
            $listElements .= '<li>' . htmlspecialchars($card->getName()) . '</li>';
        }

        return new Response(
            "<html><body><ul>{$listElements}</ul></body></html>"
        );
    }
}
