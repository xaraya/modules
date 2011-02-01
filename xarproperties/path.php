<?php

sys::import('modules.base.xarproperties.textbox');

class PathProperty extends TextBoxProperty
{
	public $id = 1038;
	public $name	 = 'item_path';
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

	public function setValue($value=null)
	{
		if ($value[0] != '/') {
			$value = '/' . $value;
		} 
		$value = preg_replace('~//+~', '/', $value);
		$value = strtolower($value);

        $this->value = $value;
    }

    public function validateValue($value = null) 
    {
		
		if (!parent::validateValue($value)) return false;
		
		$itemid = $this->objectref->properties['itemid']->value;
		$name = $this->name;
		$oldval = $this->objectref->properties[$name]->value;
		$path = $value;

		$pattern = '/^[\w\-\/]{1,}$/';
		if (!preg_match($pattern, $path)) {
			$this->invalid = xarML('Path must be at least one character long and can contain only letters, numbers, slashes, underscores and dashes.');
			$this->value = $oldval;
            return false;
		}	  

		$check = explode('/',$path);
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

		$checkpath = xarMod::apiFunc('content','user','checkpath',array('path' => $path));
		if (is_numeric($checkpath) && $checkpath != $itemid) {
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

		return true;

    }
}

?>