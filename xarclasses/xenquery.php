<?php
  /**************************************************************************\
  * xenQuery class for SQL abstraction                                       *
  * Written by Marc Lutolf (marcinmilan@xaraya.com)                          *
  \**************************************************************************/

include_once 'modules/xen/xarclasses/xaxenQuery.php';

class xenQuery extends xarQueryExt
{
    var $adjunct_tables;
    var $adjunct_fields;
    var $adjunct_conditions;
    var $adjunct_bindings;
    var $container;

//---------------------------------------------------------
// Constructor
//---------------------------------------------------------
    function xenQuery($type='SELECT',$tables='',$fields='')
    {
        parent::xaxenQuery($type,$tables,$fields);
        $this->type = $type;
        $this->adjunct_tables = array();
        $this->adjunct_fields = array();
        $this->adjunct_conditions = array();
        $this->adjunct_bindings = array();
    }

    function addadjuncttable()
    {
        $numargs = func_num_args();
        if ($numargs == 2) {
            $name = func_get_arg(0);
            $alias = func_get_arg(1);
            $argsarray = array('name' => $name, 'alias' => $alias);
        }
        elseif ($numargs == 1) {
            $table = func_get_arg(0);
            if (!is_array($table)) {
                if (!is_string($table)) {
                    $msg = xarML('The table #(1) you are trying to add needs to be a string or an array.', $table);
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR_QUERY', new SystemMessage($msg));
                    return;
                }
                else {
                    $newtable = explode('as',$table);
                    if (count($newtable) > 1) {
                        $argsarray = array('name' => trim($newtable[0]), 'alias' => trim($newtable[1]));
                    }
                    else {
                        $argsarray = array('name' => trim($newtable[0]), 'alias' => '');
                    }
                }
            }
            else {
                $argsarray = $table;
            }
        }
        else {
            $msg = xarML('This function only take 1 or 2 paramters');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemMessage($msg));
            return;
        }
    $this->adjunct_tables[] = $argsarray;
    }

    function addadjunctfield()
    {
        $numargs = func_num_args();
        if ($numargs == 2) {
            $name = func_get_arg(0);
            $value = func_get_arg(1);
            $argsarray = array('name' => $name, 'value' => $value);
        }
        elseif ($numargs == 1) {
            $field = func_get_arg(0);
            if (!is_array($field)) {
                if (!is_string($field)) {
                    $msg = xarML('The field #(1) you are trying to add needs to be a string or an array.', $field);
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR_QUERY', new SystemMessage($msg));
                    return;
                }
                else {
                    if ($this->type == 'SELECT') {
                        $argsarray = array('name' => trim($field), 'value' => '', 'alias' => '');
                    }
                    else {
                        $newfield = explode('=',$field);
                        $argsarray = array('name' => trim($newfield[0]), 'value' => trim($newfield[1]));
                    }
                }
            }
            else {
                $argsarray = $field;
            }
        }
        else {
            $msg = xarML('This function only take 1 or 2 paramters');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemMessage($msg));
            return;
        }
        $done = false;
        for ($i=0;$i<count($this->fields);$i++) {
            if ($this->fields[$i]['name'] == $argsarray['name']) {
                $this->fields[$i] = $argsarray;
                $done = true;
                break;
            }
        }
        if (!$done) $this->adjunct_fields[] = $argsarray;
    }

    function addadjunctfields($fields)
    {
        if (!is_array($fields)) {
            if (!is_string($fields)) {
            //error msg
            }
            else {
                if ($fields != '') {
                    $newfields = explode(',',$fields);
                    foreach ($newfields as $field) $this->addadjunctfield($field);
                }
            }
        }
        else {
            if ($this->type == 'SELECT') {
                foreach ($fields as $field) $this->addadjunctfield($field);
            }
            else {
                foreach ($fields as $field) $this->addadjunctfield($field);
//            $this->fields = array_merge($this->fields,$fields);
            }
        }
    }

    function addadjuncttables($tables)
    {
        if (!is_array($tables)) {
            if (!is_string($tables)) {
            //error msg
            }
            elseif ($tables=='') {}//error msg
            else {$this->addadjuncttable($tables);}
        }
        else {
            foreach ($tables as $table) $this->addadjuncttable($table);
//            $this->tables = array_merge($this->tables,$tables);
        }
    }

    function adjunctjoin($field1,$field2)
    {
        $key = $this->key;
        $this->key++;
        $numargs = func_num_args();
        if ($numargs == 2) {
            $this->adjunct_bindings[$key]=array('field1' => $field1,
                                      'field2' => $field2,
                                      'op' => 'join');
        }
        elseif ($numargs == 4) {
            $this->adjunct_bindings[$key]=array('field1' => func_get_arg(0) . "." . func_get_arg(1),
                                      'field2' => func_get_arg(2) . "." . func_get_arg(3),
                                      'op' => 'join');
        }
        return $key;
    }
    function getadjunctconditions()
    {
        $c = "";
        foreach ($this->adjunct_conditions as $condition) {
            if (is_array($condition)) {
                if (gettype($condition['field2']) == 'string' && $condition['op'] != 'join') {
                    $sqlfield = $this->dbconn->qstr($condition['field2']);
                }
                else {
                    $sqlfield = $condition['field2'];
                    $condition['op'] = $condition['op'] == 'join' ? '=' : $condition['op'];
                }
                $c .= $condition['field1'] . " " . $condition['op'] . " " . $sqlfield . " AND ";
            }
            else {
            }
        }
        if ($c != "") $c = substr($c,0,strlen($c)-5);
        return $c;
    }
// ------ Private methods -----
    function assembledadjuncttables()
    {
        $t = '';
        if (count($this->adjunct_tables) != 0) {
            foreach ($this->adjunct_tables as $table) {
                if (is_array($table)) {
                    $t .= $table['name'] . " " . $table['alias'] . ", ";
                }
                else {
                    $t .= $table . ", ";
                }
            }
            if ($t != "") $t = trim($t," ,");
            $t = "," . $t;
        }
        return $t;
    }
    function assembledadjunctfields($type)
    {
        $f = "";
        if (count($this->adjunct_fields) != 0) {;
            $f .= ",";
            foreach ($this->adjunct_fields as $field) {
                if (is_array($field)) {
                    $f .= $field['name'];
                    $f .= (isset($field['alias']) && $field['alias'] != '') ? " AS " . $field['alias'] . ", " : ", ";
                }
                else {
                    $f .= $field . ", ";
                }
            }
            if ($f != "") $f = trim($f," ,");
            $f = "," . $f;
        }
        return $f;
    }
    function assembledadjunctconditions()
    {
        $c = " ";
        if (count($this->adjunct_conditions)>0) {
            $c .= $this->getadjunctconditions();
        }
        return $c;
    }
    function _statement()
    {
        if (count($this->adjunct_tables) != 0) $this->tables = array_merge($this->tables, $this->adjunct_tables);
        if (count($this->adjunct_fields) != 0) $this->fields = array_merge($this->fields, $this->adjunct_fields);
        if (count($this->adjunct_conditions) != 0) $this->conditions = array_merge($this->conditions, $this->adjunct_conditions);
        if (count($this->adjunct_bindings) != 0) $this->bindings = array_merge($this->bindings, $this->adjunct_bindings);
        return parent::_statement();
    }
}
?>