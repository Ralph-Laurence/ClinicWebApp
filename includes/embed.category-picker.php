<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 
 
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/Category.php");

use Models\Category; 
 
class CategoryPicker
{
    private DbHelper $DB;
    private $fields = null;

    function __construct()
    { 
        global $pdo;

        $this->DB       = new DbHelper($pdo);
        $this->fields   = Category::getFields(); 
    }

    function getCategories()
    {
        try
        {
            $category = new Category($this->DB);

            $categoryDataset = $category->getAll();
        }
        catch (\Exception $ex) { IError::Throw(500); exit; }
        catch (\Throwable $th) { IError::Throw(500); exit; }

        return $categoryDataset;
    }

    function bindDataset()
    { 
        $security = new Security();

        foreach ($this->getCategories() as $obj) 
        {
            $id   = $security->Encrypt($obj[$this->fields->id]);
            $name = $obj[$this->fields->name];
            $icon = "assets/images/inventory/" . trim($obj[$this->fields->icon]) .".png";

            if (empty($obj[$this->fields->icon]))
                $icon = "assets/images/icons/icn_no_image.png";
 
            echo <<<TR
            <tr>
                <td class="th-230 text-truncate">
                    <div class="d-flex align-items-center gap-2">
                        <img src="$icon" width="20" height="20" />
                        {$name}
                    </div>
                </th>
                <td class="text-center">
                    <button type="button" class="btn btn-secondary fw-bold py-1 px-2" data-mdb-dismiss="modal" 
                    onclick="selectCategory('$id', '$name', '$icon')">
                    Select
                    </button>
                </td>
            </tr>
            TR;
        }
    }
}

$categoryPicker = new CategoryPicker();
?>

<div class="modal fade" id="findCategoryModal" tabindex="-1" aria-labelledby="findCategoryModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog" style="min-width: 600px; width: 600px;">
        <div class="modal-content mt-4">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title" id="findCategoryModalLabel">Select Category</h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-wrapper" style="overflow-y: auto; max-height: 450px; height: 450px;">
                    <div class="warning-container"></div>
                    <table class="table table-sm table-striped table-hover">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr> 
                                <th scope="col" class="fw-bold th-230">Category</th>
                                <th scope="col" class="fw-bold th-75 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="categories-tbody">
                            <?php $categoryPicker->bindDataset(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>