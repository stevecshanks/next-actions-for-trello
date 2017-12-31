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

    /**
     * @param BoardId $boardId
     * @return NamedList[]
     */
    public function fetchListsOnBoard(BoardId $boardId): array;
}
