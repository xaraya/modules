<?php
/**
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Vendors module
 */

/**
 * Supplier Property
 * @author Marc Lutolf (mfl@netspan.ch)
 */
sys::import('modules.base.xarproperties.dropdown');


    /*
    * Options available to supplier selection
    * ===================================
    * Options take the form:
    *   option-type:option-value;
    * option-types:
    *   group:name[,name] - select only suppliers of certain type(s) TODO
    *   category:name[,name] - select only suppliers of certain categories(s) TODO
    *   link:name[,name] - select only supplier(s) that are linked TODO
    *   supplier:name[,name] - select only certain supplier(s)
    */

class SupplierProperty extends SelectProperty
{
    public $id         = 30024;
    public $name       = 'supplier';
    public $desc       = 'Supplier';
    public $reqmodules = array('vendors');

    public $grouplist = array();
    public $categorylist = array();
    public $supplierlist = array();

    function __construct($args)
    {
        parent::__construct($args);
        $this->filepath   = 'modules/vendors/xarproperties';

        if (count($this->options) == 0) {
            include_once 'modules/xen/xarclasses/xenobject.class.php';
            $q = new xenQuery();
            if (!empty($this->grouplist)) {
                foreach ($this->grouplist as $group) {
//                  $q->eq('charttype',$type);
                }
            }
            if (!empty($this->categorylist)) {
                foreach ($this->categorylist as $category) {
//                  $q->eq('category',$category);
                }
            }
            if (!empty($this->supplierlist)) {
                foreach ($this->supplierlist as $supplier) {
                    $q->eq('id',$supplier);
                }
            }
            $suppliers = xarModAPIFunc('vendors',
                             'user',
                             'getallsuppliers',array('conditions' => $q));
            $with = isset($args['with']) ? $args['with'] : false;
            if ($with == 'blank') {
                $this->options[] = array('id' => '', 'name' => ' ');
            } elseif ($with == 'choose') {
                $this->options[] = array('id' => '', 'name' => xarML('Please choose an option'));
            }
            foreach ($suppliers as $supplier) {
                $this->options[] = array('id' => $supplier['id'], 'name' => $supplier['name']);
            }
        }
    }

    function parseValidation($validation = '')
    {
        foreach(preg_split('/(?<!\\\);/', $this->validation) as $option) {
            // Semi-colons can be escaped with a '\' prefix.
            $option = str_replace('\;', ';', $option);
            // An option comes in two parts: option-type:option-value
            if (strchr($option, ':')) {
                list($option_type, $option_value) = explode(':', $option, 2);
                if ($option_type == 'group') {
                    $this->grouplist = array_merge($this->grouplist, explode(',', $option_value));
                }
                if ($option_type == 'category') {
                    $this->categorylist = array_merge($this->categorylist, explode(',', $option_value));
                }
                if ($option_type == 'supplier') {
                    $this->supplierlist = array_merge($this->supplierlist, explode(',', $option_value));
                }
            }
        }
    }
}

?>