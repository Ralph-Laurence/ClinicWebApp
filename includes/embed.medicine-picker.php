<?php

require_once("rootcwd.inc.php");
  
//require_once($rootCwd . "includes/common.data.Inventory.php");

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");
require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/Stock.php");

use Models\Item;
use Models\Stock;

class MedicinePicker
{     
    private $items, $stocks, $db;

    function __construct()
    {
        global $pdo;

        $this->db = new DbHelper($pdo);

        $this->items = new Item($this->db);
        $this->stocks = new Stock($this->db);
    }

    function getMedicines()
    { 
        try 
        { 
            //$dataset = $this->items->showAll();
            $dataset = [];
            $i = $this->items->getFields();

            $tempMedicines = $this->items->showAll();

            // Find the stocks
            $s = $this->stocks->getFields();
            $stocksTable = TableNames::stock;

            $stmt_getStocks = $this->db->getInstance()->prepare
            (
                "SELECT 
                    $s->id          AS 'stockId', 
                    $s->item_id     AS 'itemId', 
                    $s->sku         AS 'sku', 
                    $s->quantity    AS 'qty' 
                FROM $stocksTable
                ORDER BY $s->expiry_date ASC"
            );
            $stmt_getStocks->execute();

            $tempStocks = $stmt_getStocks->fetchAll(PDO::FETCH_ASSOC);

            $stocks_grouped = array_reduce($tempStocks, function ($carry, $item) 
            {
                $carry[$item['itemId']][] = $item;
                return $carry;
            }, []);

            $stocx = [];
            
            foreach ($stocks_grouped as $key => $value) 
            { 
                $stocx[$key] = json_encode($value);
            }
 
            foreach ($tempMedicines as $row)
            {
                $tempRow = $row;  
                $tempRow['stockData'] = $stocx[ $row[$s->id] ];

                $dataset[$row[$i->id]] = $tempRow;
            }

            // dump($dataset);
        } 
        catch (\Exception $ex) { echo $ex->getMessage(); exit; IError::Throw(500); exit;} 
        catch (\Throwable $ex) { echo $ex->getMessage(); exit; IError::Throw(500); exit;} 
        
        return $dataset;
    }

    function bindDataset()
    {
        $security   = new Security();
        $fields     = Item::getFields(); 
        $medicines  = $this->getMedicines();

        if (empty($medicines))
            return;

        $collectExpiredIds = [];

        foreach ($medicines as $item) 
        {
            $itemId         = $security->Encrypt($item[$fields->id]);
            $remaining      = $item[$fields->remaining];
            $units          = $item['units'];
            $reserve        = $item[$fields->criticalLevel];
            $stockData      = $item['stockData']; 

            $stock = "$remaining $units";

            $stockLabel = <<<DIV
            <div class="text-truncate d-flex flex-row gap-2 text-start">
                <span class="font-green">&#x25cf;</span>
                <span class="stock-label">$stock</span>
            </div>
            DIV;

            $actionButton =
            "<button type=\"button\" class=\"btn btn-secondary fw-bold py-1 px-2 btn-select-medicine\" data-mdb-dismiss=\"modal\">
                Select
            </button>";

            if ($remaining <= 0) 
            {
                $stockLabel = "<div class=\"stock-label stock-label-soldout\">Out of Stock</div>";
                $actionButton = "";
            }

            if ($remaining <= $reserve && $remaining > 0) {
                $stockLabel = "<div class=\"stock-label stock-label-critical\">$stock</div>";
            }
  
            // Expired
            if (Dates::isPast($item['expiryDate'])) 
            {
                if ($remaining > 0) 
                {
                    $stockLabel = <<<DIV
                    <div class="stock-label stock-label-expired" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-amber'>Expired</span><br>$stock are no longer safe to use and must be discarded.">
                        <i class="fas fa-info-circle me-1"></i>
                        Expired
                    </div>
                    DIV;
                } 
                else if ($remaining == 0) 
                {
                    $collectExpiredIds[] = $item[$fields->id];
                }

                $actionButton = "";
            }
  
            echo <<<TR
            <tr id="{$item[$fields->itemCode]}">
                <td class="d-none item-key">$itemId</td>
                <td class="text-primary item-name">{$item[$fields->itemName]}</th>
                <td class="text-muted item-category">{$item['category']}</td>
                <td class="stock-label-wrapper">$stockLabel</td>
                <td class="d-none max-qty">$remaining</td>
                <td class="text-center">$actionButton</td>
                <td class="d-none stock-data">$stockData</td>
                <td class="d-none units-label">$units</td>
            </tr>
            TR;
        }

        if (!empty($collectExpiredIds))
        {
            $this->items->resetExpiryDates($collectExpiredIds);
        }
    }
} 

$medicinePicker = new MedicinePicker();
?>
<div class="modal fade" id="selectMedicineModal" tabindex="-1" aria-labelledby="selectMedicineModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content mt-4">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title" id="selectMedicineModalLabel">Select Medicine</h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="medicine-picker-toolbar d-flex align-items-center mb-2">
                    <div class="me-auto custom-tooltip">
                        <span class="custom-tooltip-title-left">
                            <div class="d-flex text-start align-items-center">
                                <i class="fas fa-info-circle me-2 text-warning"></i>
                                <span>Results will be shown as you type</span>
                            </div>
                        </span>
                        <div class="input-group" style="max-height: 36px; height: 36px;">
                            <input type="text" class="form-control medicines-searchbar" placeholder="Find Medicine" maxlength="32" style="max-height: 36px; height: 36px;" />
                            <button class="btn btn-outline-primary display-none btn-clear-search px-3" type="button" id="button-addon2" data-mdb-ripple-color="dark">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-1 d-inline">Show</div>
                        <div class="medicine-entries-filter-container"></div>
                        <div class="ms-1 d-inline">entries</div>
                    </div>
                </div>
                <div class="table-wrapper medicine-table-wrapper" style="overflow-y: auto; max-height: 400px; height: 400px;">
                    <table class="table table-sm table-striped table-hover medicine-table medicine-picker-table">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="d-none">ItemKey</th>
                                <th scope="col" class="fw-bold">Medicine</th>
                                <th scope="col" class="fw-bold">Category</th>
                                <th scope="col" class="fw-bold">Stock</th>
                                <th scope="col" class="d-none">Stock Amount</th>
                                <th scope="col" class="fw-bold text-center">Action</th>
                                <th scope="col" class="d-none stock-data">Stock Data</th>
                                <th scope="col" class="d-none units-label">Units Label</th>
                            </tr>
                        </thead>
                        <tbody class="medicine-picker-body">
                            <?php $medicinePicker->bindDataset(); ?>
                        </tbody>
                        <tfoot class="d-none">
                            <tr>
                                <th></th>
                                <th class="search-col-medicines"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>