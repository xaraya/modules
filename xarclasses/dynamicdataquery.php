<?php
/**
 * Query abstraction for DD objects
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

include_once 'modules/roles/xarincludes/xarQuery.php';

class DynamicDataQuery extends xarQuery
{
    var $table;
    var $initalias;
    var $object;
    var $columns;
    var $columnnames;
    var $columnids;
    var $fieldsempty = 1;
//---------------------------------------------------------
// Constructor
//---------------------------------------------------------
    function DynamicDataQuery($object)
    {
        if (!isset($object)) {
            $msg = xarML('DynamicDataQuery needs to be instantiated with a parameter');
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }
        $this->object = $object;

        // Load the DD API so we have access to the DD tables
        xarModAPILoad('dynamicdata');
        $tables = xarDBGetTables();

        // -------------------------------------------------------
        // First we will create and save an array which holds the column info
        // Create an empty query object
        // This defaults to a SELECT
        parent::xarQuery();

        // Add the DD properties table to query from
        parent::addtable($tables['dynamic_properties']);

        // Specify which fields we are interested in
        // We could leave this line out, in which case ALL fields are selected from
        // but we specify the fields here for performance reasons, and also
        // so that we can use aliases to make the field names more intelligeable
        // The nice thing about using DD is that the tables have a known structure,
        // so all we really need is the first 2 or 3 columns
        parent::addfield('xar_prop_id as colid');
        parent::addfield('xar_prop_name as name');
        parent::addfield('xar_prop_label as label');
        parent::addfield('xar_prop_type as type');

        // Which object?
        // Add the next line so we only get data from the encyclopedia object
        // TODO: use this line to subclass the rest of the code, which can
        // be left as a generic DynamicDataQuery
        parent::eq('xar_prop_objectid', $this->object);

        // Get the column data and rearrange
        if(!parent::run()) return;
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
        parent::xarQuery();

        // The query will get its data from the dynamic_data table
        // whose fields are known
        $this->initalias = "init".time();
        $init = $this->initalias;
        $this->table = $tables['dynamic_data'];
        parent::addtable($this->table, $init);
        parent::addfield($init . '.xar_dd_propid as propid');
        parent::addfield($init . '.xar_dd_itemid as itemid');
        parent::addfield($init . '.xar_dd_value as value');

        // Restrict the query to the records which contain data
        // that belong to the object we are interested in
        parent::in($this->initalias . '.xar_dd_propid',$this->columnids);
    }

    function output()
    {
        $items = array();
        foreach(parent::output() as $datum) {
            $cols = $this->columns;
            $items[$datum['itemid']][$cols[$datum['propid']]['name']] = $datum['value'];
        }
        return $items;
    }

    function addfield($field)
    {
        // Disable this for the moment
//        $this->fieldsempty = 0;
//        parent::eq($this->initalias . '.xar_dd_propid',$this->columnnames[$field]);
    }
    function addfields($fields) {
        foreach ($fields as $field) $this->addfield($field);
    }

    function adjunctclause($field)
    {
        sleep(1);
        $this->subalias = "sub".time();
        parent::addtable($this->table, $this->subalias);
        parent::join($this->initalias . '.xar_dd_itemid', $this->subalias . '.xar_dd_itemid');
        parent::eq($this->subalias . '.xar_dd_propid',$this->columnnames[$field]);
    }
    function eq($field1,$field2)
    {
        $this->adjunctclause($field1);
        parent::eq($this->subalias . '.xar_dd_value',$field2);
    }
    function like($field1,$field2)
    {
        $this->adjunctclause($field1);
        parent::like($this->subalias . '.xar_dd_value',$field2);
    }

    function addorder($x = '',$y = 'ASC')
    {
        $this->adjunctclause($x);
        parent::addorder($this->subalias . '.xar_dd_value',$y);
    }

    // Disable this for the moment
//    function run($statement='',$display=1) {
//        parent::in($this->initalias . '.xar_dd_propid',$this->columnids);
//        parent::run($statement,$display);
//    }
}
?>