<?php

namespace App\Trello;

interface Api
{
    /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array;

    /**
     * @param ListId $listId
     * @return Card[]
     */
    public function fetchCardsOnList(ListId $listId): array;
}
