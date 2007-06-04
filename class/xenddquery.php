<?php
/**
 * Query abstraction for DD objects
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Query Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

sys::import('modules.query.class.query');

class DDQuery extends Query
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
    function DDQuery($object=null)
    {
    /*
        if (!isset($object)) {
            $msg = xarML('DynamicDataQuery needs to be instantiated with a parameter');
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }
    */
        if(empty($object)) return false;
        // TODO: introduce array notation to make this more robust?
        if (!is_numeric($object)) {
            $objectInfo = xarModApiFunc('dynamicdata','user','getobjectinfo', array('name' => $object));
            $object = $objectInfo['objectid'];
            if(empty($object)) return false;
        }
        // This is a reference id to the DD object this query is about
        $this->object = array();
        $ancestors = xarModAPIFunc('dynamicdata','user','getancestors',array('objectid' => $object));
        foreach ($ancestors as $ancestor) $this->object[] = $ancestor['objectid'];

        // Load the DD API so we have access to the DD tables
        xarModAPILoad('dynamicdata');
        $tables = xarDB::getTables();

        // -------------------------------------------------------
        // First we will create and save an array which holds the column info
        // Create an empty query object
        // This defaults to a SELECT
        parent::__construct();
        parent::setdistinct();

        // Add the DD properties table to query from
        parent::addtable($tables['dynamic_properties']);

        // Specify which fields we are interested in
        // We could leave this line out, in which case ALL fields are selected from
        // but we specify the fields here for performance reasons, and also
        // so that we can use aliases to make the field names more intelligeable
        // The nice thing about using DD is that the tables have a known structure,
        // so all we really need is the first 2 or 3 columns
        parent::addfield('prop_id as colid');
        parent::addfield('prop_name as name');
        parent::addfield('prop_label as label');
        parent::addfield('prop_type as type');

        // Which object?
        // Add the next line so we only get data from the encyclopedia object
        // TODO: use this line to subclass the rest of the code, which can
        // be left as a generic DynamicDataQuery
        parent::in('prop_objectid', $this->object);

        // Only include active fields
        // TODO: adjust the query for property status
//        parent::eq('prop_status', 1);

        // Get the column data and rearrange
        if(!parent::run()) return;
        $columns = array();
        $columnnames = array();
        $colids = array();
        foreach(parent::output() as $column) {
            $columns[$column['colid']] = array('name' => $column['name'], 'label' => $column['label'], 'type' =>$column['type']);
            $columnnames[$column['name']] = $column['colid'];
            $colids[] = $column['colid'];
        }

        // Save the information for later use
        $this->columns = $columns;
        $this->columnnames = $columnnames;
        $this->columnids = $colids;

        // -------------------------------------------------------
        // Now instantiate the query object we'll actually be using
        parent::__construct();

        // The query will get its data from the dynamic_data table
        // whose fields are known
        $this->initalias = "init".time();
        $init = $this->initalias;
        $this->table = $tables['dynamic_data'];
        parent::addtable($this->table, $init);
        parent::addfield($init . '.dd_propid as propid');
        parent::addfield($init . '.dd_itemid as itemid');
        parent::addfield($init . '.dd_value as value');

        // Restrict the query to the records which contain data
        // that belong to the object we are interested in
        parent::in($this->initalias . '.dd_propid',$this->columnids);
    }

    function output()
    {
        $items = array();
        foreach(parent::output() as $datum) {
            $cols = $this->columns;
            $items[$datum['itemid']][$cols[$datum['propid']]['name']] = $datum['value'];
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

    function addfield($field)
    {
        // Disable this for the moment
        $this->fieldsempty = 0;
        parent::eq($this->initalias . '.dd_propid',$this->columnnames[$field]);
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
        parent::join($this->initalias . '.dd_itemid', $this->subalias . '.dd_itemid');
        parent::eq($this->subalias . '.dd_propid',$this->columnnames[$field]);
    }
    function join($field1,$field2)
    {
        // Nice try, but this doesn't work
        $this->adjunctclause($field1);
        parent::join($this->subalias . '.dd_value',$field2);
    }
    function eq($field1,$field2)
    {
        $this->adjunctclause($field1);
        parent::eq($this->subalias . '.dd_value',$field2);
    }
    function ne($field1,$field2)
    {
        $this->adjunctclause($field1);
        parent::ne($this->subalias . '.dd_value',$field2);
    }
    function ge($field1,$field2)
    {
        $this->adjunctclause($field1);
        parent::ge($this->subalias . '.dd_value',$field2);
    }
    function le($field1,$field2)
    {
        $this->adjunctclause($field1);
        parent::le($this->subalias . '.dd_value',$field2);
    }
    function like($field1,$field2)
    {
        $this->adjunctclause($field1);
        parent::like($this->subalias . '.dd_value',$field2);
    }

    function addorder($x = '',$y = 'ASC')
    {
        $this->adjunctclause($x);
        parent::addorder($this->subalias . '.dd_value',$y);
    }

    // Disable this for the moment
//    function run($statement='',$display=1)
//    {
//        parent::in($this->initalias . '.dd_propid',$this->columnids);
//        parent::run($statement,$display);
//    }
}
?>