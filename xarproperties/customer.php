<?php
/**
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Customers module
 */

/**
 * Customer Property
 * @author Marc Lutolf (mfl@netspan.ch)
 */
sys::import('modules.base.xarproperties.dropdown');


    /*
    * Options available to customer selection
    * ===================================
    * Options take the form:
    *   option-type:option-value;
    * option-types:
    *   group:name[,name] - select only customers of certain type(s) TODO
    *   category:name[,name] - select only customers of certain categories(s) TODO
    *   link:name[,name] - select only customer(s) that are linked TODO
    *   customer:name[,name] - select only certain customer(s)
    */

class CustomerProperty extends SelectProperty
{
    public $id         = 30011;
    public $name       = 'customer';
    public $desc       = 'Customer';
    public $reqmodules = array('customers');

    public $grouplist = array();
    public $categorylist = array();
    public $customerlist = array();

    function __construct($args)
    {
        parent::__construct($args);
        $this->filepath   = 'modules/customers/xarproperties';

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
            if (!empty($this->customerlist)) {
                foreach ($this->customerlist as $customer) {
                    $q->eq('id',$customer);
                }
            }
            $customers = xarModAPIFunc('customers',
                             'user',
                             'getall',array('conditions' => $q));
            $with = isset($args['with']) ? $args['with'] : false;
            if ($with == 'blank') {
                $this->options[] = array('id' => '', 'name' => ' ');
            } elseif ($with == 'choose') {
                $this->options[] = array('id' => '', 'name' => xarML('Please choose an option'));
            }
            foreach ($customers as $customer) {
                $this->options[] = array('id' => $customer['id'], 'name' => $customer['name']);
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
                if ($option_type == 'customer') {
                    $this->customerlist = array_merge($this->customerlist, explode(',', $option_value));
                }
            }
        }
    }
}

?>