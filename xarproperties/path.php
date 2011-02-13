<?php
/**
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://http://xaraya.com/index.php/release/eid/1172
 * @author potion <ryan@webcommunicate.net>
 */
sys::import('modules.base.xarproperties.textbox');

class PathProperty extends TextBoxProperty
{
	public $id = 1038;
	public $name	 = 'path';
    public $desc	= 'Path'; 
    public $reqmodules = array('content');

	function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

		$this->include_reference = 1;

        // Set for runtime
        $this->tplmodule = 'content';
        $this->template = 'path';
		$this->filepath   = 'modules/content/xarproperties';
    }

	/*public function setValue($value=null)
	{  
		
		$this->value = $value;

    }*/

    public function validateValue($value = null) 
    {
		
		if (!parent::validateValue($value)) return false;
		
		$itemid = $this->objectref->properties['itemid']->value;
		$name = $this->name;
		$oldval = $this->objectref->properties[$name]->value;
	
		$path = preg_replace('~//+~', '/', $value);

		$alphanum = preg_replace('/[^a-zA-Z0-9]/','',$path);
		if (empty($alphanum)) {
			$this->invalid = xarML('Illegal path. Path must contain at least one letter or number.');
			$this->value = $oldval;
            return false;
		}

		$pattern = '/^[\w\-\/ ]{1,}$/';
		if (!preg_match($pattern, $path)) {
			$this->invalid = xarML('Path must be at least one character long and can contain only letters, numbers, forward slashes, underscores and dashes.');
			$this->value = $oldval;
            return false;
		}	  
 
		$check = explode('/',$path);
		if (isset($check[2])) {
			if (is_numeric($check[2])) {
				$this->invalid = xarML('Numeric values are not permitted in the 2nd part of the path.');
				$this->value = $oldval;
				return false;
			}
			if ($check[2] == 'view') { 
				$this->invalid = xarML('Invalid path. The second part of the path must not be \'view\'.');
				$this->value = $oldval;
				return false;
			}
		}
		$checkpath = xarMod::apiFunc('content','user','checkpath',array('path' => $path));
		if (is_numeric($itemid) && is_numeric($checkpath) && $checkpath != $itemid) {
			$this->invalid = xarML("The path you've specified is already in use.  Please try again.");
			$this->value = $oldval;
            return false;
		} 

		$aliascheck = xarMod::apiFunc('content','user','alias',array('path' => $path));

		if(is_string($aliascheck)) {   
			$this->invalid = xarML('The pathstart #(1) is the name of an installed module.', $aliascheck);
			$this->value = $oldval;
            return false;
		}

		if (is_array($aliascheck)) {
			// aliasmodule for use in custom error messages
			$this->invalid = xarML('The pathstart #(1) is the alias for an installed module.', $aliascheck);
			$this->value = $oldval;
            return false;
		}	

		if (!empty($this->value) && $this->value != '/') { 
			$value = $this->value;
			if ($value[0] != '/') {
				$value = '/' . $value;
			} 
			$value = preg_replace('~//+~', '/', $value);
			$value = strtolower($value);
			$path = str_replace(' ', '_', $value);
			$this->value = $path;
			
			//See if we can register an alias
			$str = substr($path, 1);
			$pos = strpos($str, '/');
			if ($pos) {
				$pathstart = substr($str, 0, $pos);
			} else {
				$pathstart = $str;
			}
			$aliases = xarConfigVars::get(null, 'System.ModuleAliases');
			if (!isset($aliases[$pathstart])) { 
				// $pathstart is not an alias, so register one...
				xarModAlias::set($pathstart, 'content');
			}
		}

		return true;

    }
}

?>