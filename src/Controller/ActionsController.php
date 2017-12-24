<?php

namespace App\Controller;

use App\Trello\Auth;
use App\Trello\JsonApi;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionsController
{
    /**
     * @Route("/actions")
     *
     * @return Response
     */
    public function list()
    {
        $api = new JsonApi(
            new Client(),
            new Auth($_SERVER['TRELLO_API_KEY'], $_SERVER['TRELLO_API_TOKEN'])
        );
        $cards = $api->fetchCardsIAmAMemberOf();

        $listElements = "";
        foreach ($cards as $card) {
            $listElements .= '<li>' . htmlspecialchars($card->getName()) . '</li>';
        }

        return new Response(
            "<html><body><ul>{$listElements}</ul></body></html>"
        );
    }
}
