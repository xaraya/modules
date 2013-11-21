<?php
/**
 * Query abstraction for EAV objects
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Query Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

/**
 * This extension lets you use the usual Query notation on an EAV structure
 * It uses the multi join method discussed here
 * http://stackoverflow.com/questions/8764290/what-is-best-performance-for-retrieving-mysql-eav-results-as-relational-table
 */

sys::import('xaraya.structures.query');

class EAVQuery extends Query
{
    public $table;
    public $initalias;
    public $object;
    public $columns;
    public $columnnames;
    public $columnids;
    public $fieldsempty = 1;
//---------------------------------------------------------
// Constructor
//---------------------------------------------------------
    function EAVQuery($object_id=null)
    {
        // We need an object ID
        if(empty($object_id) || !is_numeric($object_id)) return false;

        // Load the DD API so we have access to the DD tables
        // Superfluous if we're coming from the EAV module, but we cannot know that
        xarMod::apiLoad('eav');
        $tables =& xarDB::getTables();

        // -------------------------------------------------------
        // First we will create and save an array which holds the column info
        // Create an empty query object
        // This defaults to a SELECT
        parent::__construct();
        parent::setdistinct();

        // Add the EAV attributes table to query from
        parent::addtable($tables['eav_attributes']);

        // Specify which fields we are interested in
        // We could leave this line out, in which case ALL fields are selected from
        // but we specify the fields here for performance reasons, and also
        // so that we can use aliases to make the field names more intelligeable
        // The nice thing about using DD is that the tables have a known structure,
        // so all we really need is the first 2 or 3 columns
        parent::addfield('id as columnid');
        parent::addfield('name as name');
        parent::addfield('label as label');
        parent::addfield('type as type');

        // Add the next line so we only get data from the object we are interested in
        // TODO: use this line to subclass the rest of the code, which can
        // be left as a generic DynamicDataQuery
        parent::in('object_id', $object_id);

        // Only include active fields
        // TODO: adjust the query for property status
//        parent::eq('status', 1);

        // Get the column data and rearrange
        if(!parent::run()) return;
        $columns = array();
        $columnnames = array();
        $colummids = array();
        foreach(parent::output() as $column) {
            $columns[$column['columnid']] = array('name' => $column['name'], 'label' => $column['label'], 'type' =>$column['type']);
            $columnnames[$column['name']] = $column['columnid'];
            $colummids[] = $column['columnid'];
        }

        // Save the information for later use
        $this->columns = $columns;
        $this->columnnames = $columnnames;
        $this->columnids = $colummids;

        // -------------------------------------------------------
        // Now instantiate the query object we'll actually be using
        parent::__construct();

        // The query will get its data from the eav_data table
        // whose fields are known
        $this->initalias = "init".time();
        $init = $this->initalias;
        $this->table = $tables['eav_data'];
        parent::addtable($this->table, $init);
        parent::addfield($init . '.attribute_id');
        parent::addfield($init . '.item_id');
        parent::addfield($init . '.value');

        // Restrict the query to the records which contain data
        // that belong to the object we are interested in
        parent::in($this->initalias . '.attribute_id',$this->columnids);
    }

    function output()
    {
        $items = array();
        foreach(parent::output() as $datum) {
            $cols = $this->columns;
            $items[$datum['item_id']][$cols[$datum['attribute_id']]['name']] = $datum['value'];
        }
        // TODO: find a better way to do this
        $output = array();
        $i=0;
        foreach($items as $item) {
            $output[$i] = $item;
            $i++;
        }
        return $output;
    }

    function row($row=0)
    {
        $rows = $this->output();
        if (empty($rows)) return array();
        return $rows[$row];
    }

    function addfield()
    {
        $numargs = func_num_args();
        $field = '';
        if ($numargs == 1) {
            $field = func_get_arg(0);
        }

        // Disable this for the moment
        $this->fieldsempty = 0;
        parent::eq($this->initalias . '.attribute_id',$this->columnnames[$field]);
    }
//    function addfields($fields)
//    {
//        foreach ($fields as $field) $this->addfield($field);
//    }

    function adjunctclause($field)
    {
        sleep(1);
        $this->subalias = "sub".time();
        parent::addtable($this->table, $this->subalias);
        parent::join($this->initalias . '.item_id', $this->subalias . '.item_id');
        parent::eq($this->subalias . '.attribute_id',$this->columnnames[$field]);
    }
    function join($field1,$field2,$active=1)
    {
        // Nice try, but this doesn't work
        $this->adjunctclause($field1);
        parent::join($this->subalias . '.value',$field2);
    }
    function eq($field1,$field2,$active=1)
    {
        $this->adjunctclause($field1);
        parent::eq($this->subalias . '.value',$field2);
    }
    function ne($field1,$field2,$active=1)
    {
        $this->adjunctclause($field1);
        parent::ne($this->subalias . '.value',$field2);
    }
    function ge($field1,$field2,$active=1)
    {
        $this->adjunctclause($field1);
        parent::ge($this->subalias . '.value',$field2);
    }
    function le($field1,$field2,$active=1)
    {
        $this->adjunctclause($field1);
        parent::le($this->subalias . '.value',$field2);
    }
    function like($field1,$field2,$active=1)
    {
        $this->adjunctclause($field1);
        parent::like($this->subalias . '.value',$field2);
    }

    function addorder($x = '',$y = 'ASC')
    {
        $this->adjunctclause($x);
        parent::addorder($this->subalias . '.value',$y);
    }

    // Disable this for the moment
//    function run($statement='',$display=1)
//    {
//        parent::in($this->initalias . '.property_id',$this->columnids);
//        parent::run($statement,$display);
//    }
}
?>