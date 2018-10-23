<?php

declare(strict_types=1);

namespace App\UserModule\Controls\ClientRecordListGrid;

final class ClientRecordListGridControl extends \Ublaboo\DataGrid\DataGrid
{

    /**
     * @var \App\UserModule\Model\RecordService
     */
    private $recordService;



    public function __construct(
        \App\UserModule\Model\RecordService $recordService,
        int $userId
    ) {
        parent::__construct();
        $this->recordService = $recordService;



        $this->setDataSource($recordService->getRecordTable()->where('klient_id', $this->recordService->getClientIdByUserId($userId)));
        $this->setItemsPerPageList([10, 20, 50]);
        $this->setRememberState(false);
        $this->onColumnAdd[] = function ($key, \Ublaboo\DataGrid\Column\Column $column): void {
            $column->setSortable();
        };

        $this->addColumnText('nazev_akce', 'Název akce', "")
            ->setFilterText();

        $this->addColumnDateTime('datum_zapujceni', 'Datum zapujčení')
            ->setFilterDate();

        $this->addColumnDateTime('datum_vraceni', 'Datum vrácení')
            ->setFilterDate();

        $this->addColumnText('kostym_id', 'Kostým', 'kostym.popis')
            ->setFilterText();

        $this->addColumnText('doplnek_id', 'Doplněk', 'doplnek.nazev')
            ->setFilterText();

        $this->addColumnText('cena', 'Cena')
            ->setFilterText();


        $this->addActionDetails();

    }

    /**
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function addActionDetails(
        ?callable $condition = null,
        string $href = 'details',
        array $params = null
    ): \Ublaboo\DataGrid\Column\Action {
        $action = $this->addAction('datails', '', $href, $params);
        $action->setTitle('Detaily')
            ->setClass('btn btn-xs btn-primary')
            ->setIcon('eye');

        if ($condition) {
            $this->allowRowsAction('details', function ($item) use ($condition): bool {
                return $condition($item);
            });
        }

        return $action;
    }
}
