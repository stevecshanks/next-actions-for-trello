<?php

namespace App\Controller;

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
        return new Response(
            '<html><body>Test a walking skeleton</body></html>'
        );
    }
}
