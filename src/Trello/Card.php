<?php

namespace App\Trello;

use Cake\Chronos\Chronos;
use DateTimeInterface;
use JsonSerializable;
use stdClass;

class Card implements JsonSerializable
{
    const BASE_URL = 'https://trello.com/c';
    const DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    /** @var string */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $description;
    /** @var string */
    protected $url;
    /** @var Board */
    protected $board;
    /** @var DateTimeInterface|null */
    protected $dueDate;
    /** @var Label[] */
    protected $labels;
    /** @var Checklist[] */
    protected $checklists;

    /**
     * Card constructor.
     * @param string $id
     * @param string $name
     * @param string $description
     * @param string $url
     * @param Board $board
     * @param DateTimeInterface|null $dueDate
     * @param Label[] $labels
     * @param Checklist[] $checklists
     */
    public function __construct(
        string $id,
        string $name,
        string $description,
        string $url,
        Board $board,
        ?DateTimeInterface $dueDate,
        array $labels,
        array $checklists
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->board = $board;
        $this->dueDate = $dueDate;
        $this->labels = $labels;
        $this->checklists = $checklists;
    }

    public static function fromJson(stdClass $json, Board $board)
    {
        return new static(
            $json->id,
            $json->name,
            $json->desc,
            $json->url,
            $board,
            $json->due ? Chronos::createFromFormat(static::DATE_FORMAT, $json->due) : null,
            array_map([Label::class, 'fromJson'], $json->labels),
            array_map([Checklist::class, 'fromJson'], $json->checklists)
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDueDate(): ?DateTimeInterface
    {
        return $this->dueDate;
    }

    /**
     * @return Label[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @return Checklist[]
     */
    public function getChecklists(): array
    {
        usort(
            $this->checklists,
            function (Checklist $checklist1, Checklist $checklist2) {
                return $checklist1->getPosition() <=> $checklist2->getPosition();
            }
        );
        return $this->checklists;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->description,
            'url' => $this->url,
            'idBoard' => $this->board->getId(),
            'due' => $this->dueDate ? $this->dueDate->format(static::DATE_FORMAT) : null,
            'labels' => $this->labels,
            'checklists' => $this->checklists
        ];
    }
}
