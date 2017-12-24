<?php

namespace App\Trello;

interface Api
{
    /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array;
}
