<?php
include_once(GALAXIA_LIBRARY.'/src/API/BaseActivity.php');
//!! Join
//! Join class
/*!
This class handles activities of type 'join'
*/
class Join extends BaseActivity {

	function __construct($db)
	{
        parent::__construct($db);
        $this->type='join';
	}
}
?>
