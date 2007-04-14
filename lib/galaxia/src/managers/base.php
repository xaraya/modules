<?php
include_once(GALAXIA_LIBRARY.'/src/common/base.php');
//!! Abstract class representing the base of the API
//! An abstract class representing the API base
/*!
This class is derived by all the API classes so they get the
database connection, database methods and the Observable interface.
*/
class BaseManager extends Base
{
    public $error='';

    /**
     * @todo This doesn't belong here
    **/
    function get_error()
    {
        return $this->error;
    }
} //end of class

?>
