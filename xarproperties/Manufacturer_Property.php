<?php
/**
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Vendors module
 */

/**
 * Manufacturer Property
 * @author Marc Lutolf (mfl@netspan.ch)
 */
include_once "modules/base/xarproperties/Dynamic_Select_Property.php";


    /*
    * Options available to manufacturer selection
    * ===================================
    * Options take the form:
    *   option-type:option-value;
    * option-types:
    *   group:name[,name] - select only manufacturers of certain type(s) TODO
    *   category:name[,name] - select only manufacturers of certain categories(s) TODO
    *   link:name[,name] - select only manufacturer(s) that are linked TODO
    *   manufacturer:name[,name] - select only certain manufacturer(s)
    */

class Manufacturer_Property extends Dynamic_Select_Property
{
    public $grouplist = array();
    public $categorylist = array();
    public $manufacturerlist = array();

    function __construct($args)
    {
    	parent::__construct($args);
		$this->filepath   = 'modules/vendors/xarproperties';

        if (count($this->options) == 0) {
			include_once 'modules/xen/xarclasses/xenobject.class.php';
        	$q = new xenQuery();
            if (!empty($this->grouplist)) {
            	foreach ($this->grouplist as $group) {
//            		$q->eq('charttype',$type);
            	}
            }
            if (!empty($this->categorylist)) {
            	foreach ($this->categorylist as $category) {
//            		$q->eq('category',$category);
            	}
            }
            if (!empty($this->manufacturerlist)) {
            	foreach ($this->manufacturerlist as $manufacturer) {
            		$q->eq('id',$manufacturer);
            	}
            }
            $manufacturers = xarModAPIFunc('vendors',
                             'user',
                             'getallmanufacturers',array('conditions' => $q));
        	$with = isset($args['with']) ? $args['with'] : false;
        	if ($with == 'blank') {
                $this->options[] = array('id' => '', 'name' => ' ');
        	} elseif ($with == 'choose') {
                $this->options[] = array('id' => '', 'name' => xarML('Please choose an option'));
        	}
            foreach ($manufacturers as $manufacturer) {
                $this->options[] = array('id' => $manufacturer['id'], 'name' => $manufacturer['name']);
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
				if ($option_type == 'manufacturer') {
					$this->manufacturerlist = array_merge($this->manufacturerlist, explode(',', $option_value));
				}
			}
		}
    }

    static function getRegistrationInfo()
    {
        $info = new PropertyRegistration();
        $info->reqmodules = array('manufacturers');
        $info->id   = 30011;
        $info->name = 'manufacturer';
        $info->desc = 'Manufacturer';
        return $info;
    }
}

?>