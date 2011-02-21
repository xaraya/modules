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

    public function validateValue($value = null) 
    {
		
		if (!parent::validateValue($value)) return false;
		 
		// Allow empty path field... but since the item_path table field is a unique index, we set it to a value that's sure to be unique.   The encode_shorturl function will treat such values as if no path is set.
		if ($value == '') {
			if ($this->objectref->properties['itemid']->value != 0) {
				$num = $this->objectref->properties['itemid']->value;
			} else {
				$num = $this->objectref->maxid + 1;
			}
			$this->value = '/_'.$num.'_';
			return true;
		}

		// Standardize the path
		if ($value[0] != '/') {
			$value = '/' . $value;
		}
		$value = preg_replace('~//+~', '/', $value);
		$value = strtolower($value);
		$path = str_replace(' ', '_', $value); 
		
		$name = $this->name;
		$oldval = $this->objectref->properties[$name]->value;

		if ($path == '/') {
			$this->invalid = xarML('Invalid path.  The path you entered is reserved for your homepage.');
			$this->value = $oldval;
            return false;
		}	

		// Remove a trailing slash
		$num = strlen($path) - 1;
		if ($path[$num] == '/') {
			$path = substr($path,0,-1);
		} 

		$pattern = '/^[\w\-\/ ]{1,}$/';
		if (!preg_match($pattern, $path)) {
			$this->invalid = xarML('Invalid path.  Path must be at least one character long and can contain only letters, numbers, forward slashes, underscores and dashes.');
			$this->value = $oldval;
            return false;
		}	  
 
		$check = explode('/',$path);
		if (isset($check[2])) {
			if (is_numeric($check[2])) {
				// Reserve some formats for short URLs that don't need a path lookup
				$this->invalid = xarML('Invalid path.  The 2nd part of the path cannot be a number.');
				$this->value = $oldval;
				return false;
			}
			if ($check[2] == 'view') { 
				$this->invalid = xarML('Invalid path. The second part of the path must not be \'view\'.');
				$this->value = $oldval;
				return false;
			}
		}
		
		$itemid = $this->objectref->properties['itemid']->value;
		$checkpath = xarMod::apiFunc('content','user','checkpath',array('path' => $path));
		if (is_numeric($checkpath) && $itemid != $checkpath) {
			$this->invalid = xarML("The path you've specified is already in use.  Please try again.");
			$this->value = $oldval;
            return false;
		} 

		$aliascheck = xarMod::apiFunc('content','user','alias',array('path' => $path));

		if(is_string($aliascheck)) {   
			$this->invalid = xarML('Invalid path.  The pathstart ("#(1)") is the name of an installed module.', $aliascheck);
			$this->value = $oldval;
            return false;
		}

		if (is_array($aliascheck)) {
			$this->invalid = xarML('Invalid path.  The pathstart ("#(1)") is an alias for the #(2) module.', $aliascheck['pathstart'], $aliascheck['aliasmodule']);
			$this->value = $oldval;
            return false;
		}	
			
		$this->value = $path;

		return true;

    }
}

?>