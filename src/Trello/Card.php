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
    /** @var BoardId */
    protected $boardId;
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
     * @param BoardId $boardId
     * @param DateTimeInterface|null $dueDate
     * @param Label[] $labels
     * @param Checklist[] $checklists
     */
    public function __construct(
        string $id,
        string $name,
        string $description,
        string $url,
        BoardId $boardId,
        ?DateTimeInterface $dueDate,
        array $labels,
        array $checklists
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->boardId = $boardId;
        $this->dueDate = $dueDate;
        $this->labels = $labels;
        $this->checklists = $checklists;
    }

    public static function fromJson(stdClass $json)
    {
        $labels = array_map(
            function (stdClass $jsonLabel) {
                return new Label($jsonLabel->name);
            },
            $json->labels
        );
        $checklists = array_map(
            function (stdClass $jsonChecklist) {
                return new Checklist(array_map(
                    function (stdClass $jsonChecklistItem) {
                        return new ChecklistItem(
                            $jsonChecklistItem->name,
                            $jsonChecklistItem->state,
                            $jsonChecklistItem->pos
                        );
                    },
                    $jsonChecklist->checkItems
                ));
            },
            $json->checklists
        );

        return new static(
            $json->id,
            $json->name,
            $json->desc,
            $json->url,
            new BoardId($json->idBoard),
            $json->due ? Chronos::createFromFormat(static::DATE_FORMAT, $json->due) : null,
            $labels,
            $checklists
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
     * @return BoardId
     */
    public function getBoardId(): BoardId
    {
        return $this->boardId;
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
        return $this->checklists;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        $labels = array_map(
            function (Label $label) {
                return $label->jsonSerialize();
            },
            $this->labels
        );
        $checklists = array_map(
            function (Checklist $checklist) {
                return $checklist->jsonSerialize();
            },
            $this->checklists
        );

        return [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->description,
            'url' => $this->url,
            'idBoard' => $this->boardId->getId(),
            'due' => $this->dueDate ? $this->dueDate->format(static::DATE_FORMAT) : null,
            'labels' => $labels,
            'checklists' => $checklists
        ];
    }
}
