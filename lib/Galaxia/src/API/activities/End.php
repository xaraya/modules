<?php
include_once(GALAXIA_LIBRARY.'/src/API/BaseActivity.php');
//!! End
//! End class
/*!
This class handles activities of type 'end'
*/
class End extends BaseActivity {
    
	function __construct($db)
	{
        parent::__construct($db);
        $this->type='end';
	}
}
?>
