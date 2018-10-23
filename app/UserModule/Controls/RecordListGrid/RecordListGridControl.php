<?php

declare(strict_types=1);

namespace App\UserModule\Controls\RecordListGrid;

final class RecordListGridControl extends \IIS\Application\UI\BaseControl
{
    /**
     * @var \App\CoreModule\Controls\DataGrid\IDataGridControlFactory
     */
    private $dataGridControlFactory;

    /**
     * @var \App\UserModule\Model\RecordService
     */
    private $recordService;


    public function __construct(
        \App\CoreModule\Controls\DataGrid\IDataGridControlFactory $dataGridControlFactory,
        \App\UserModule\Model\RecordService $recordService
    ) {
        parent::__construct();
        $this->dataGridControlFactory = $dataGridControlFactory;
        $this->recordService = $recordService;
    }


    /**
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
     */
    protected function createComponentGrid(): \App\CoreModule\Controls\DataGrid\DataGridControl
    {
        $grid = $this->dataGridControlFactory->create($this->recordService);

        $grid->addColumnText('nazev_akce', 'Název akce', "")
            ->setFilterText();

        $grid->addColumnDateTime('datum_zapujceni', 'Datum zapujčení')
            ->setFilterDate();

        $grid->addColumnDateTime('datum_vraceni', 'Datum vrácení')
            ->setFilterDate();

        $grid->addColumnText('kostym_id', 'Kostým', 'kostym.popis')
            ->setFilterText();

        $grid->addColumnText('doplnek_id', 'Doplněk', 'doplnek.nazev')
            ->setFilterText();


        $grid->addColumnText('zamestnanec_id', 'Zaměstnanec', 'zamestnanec.prijmeni')
            ->setFilterText();

        $grid->addColumnText('klient_id', 'Klient', 'klient.prijmeni')
            ->setFilterText();

        $grid->addActionDetails();

        return $grid;
    }


    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\InvalidArgumentException
     */
    public function onActiveChange(int $id, bool $active): void
    {
        $presenter = $this->getPresenter();
        if ($presenter instanceof \App\CoreModule\Presenters\SecuredPresenter) {
            $presenter->checkPermission(\App\UserModule\Model\AuthorizatorFactory::ACTION_DELETE);
        }

        $this->userService->changeActive($id, $active);

        if ($presenter) {
            if ($presenter->isAjax()) {
                $grid = $this->getComponent('grid');
                if ($grid instanceof \Ublaboo\DataGrid\DataGrid) {
                    $grid->redrawItem($id);
                }
            } else {
                $presenter->redirect('this');
            }
        }
    }
}
