<?php
  /**************************************************************************\
  * xenObject class                                                          *
  * Written by Marc Lutolf (marcinmilan@xaraya.com)                          *
  \**************************************************************************/

include_once 'modules/xen/xarclasses/xenquery.php';

class xenObject{

    var $xenschema;
    var $xenquery;
    var $prefix_in = "xarin";
    var $prefix_out = "xarout";
    var $prefix_reg = "register";

//---------------------------------------------------------
// Constructor
//---------------------------------------------------------
    function xenObject($args='')
    {
        $varsout = array(
                    'id',
                    'name',
                    'description'
                    );
        $varsin = array(
                    'id',
                    'name',
                    'description'
                    );
        $varsreg = array(
                    'id',
                    'oid',
                    'name',
                    'sourceid',
                    'sourceaddress',
                    'sourcecontainer',
                    'sourceinstance',
                    'sourceidentifier'
                    );

//        $this->createVarSet($prefix_in,$varsin);
//        $this->createVarSet($prefix_out,$varsout);
//        $this->createVarSet($prefix_reg,$varsreg);

//        $xentable =& xarDBGetTables();
//        $this->registertable = $xentable['system_register'];

//        if (empty($args['id'])) {$args['id'] = 0;}
//        if (empty($args['name'])) {
//            xarExceptionSet(XAR_SYSTEM_EXCEPTION,
//                'BAD_PARAM',
//                new SystemException('Cannot create object'));
//            return false;
//        }
//        $this->setVarSet('register',$args);
    }
//---------------------------------------------------------

    function toString()
    {
        return $register_name . ": " . $xenout_description;
    }

    function toArray()
    {
        return $this->getVarSet('register');
    }

    function getContentNames()
    {
        return $this->getVarSetNames('content');
    }

    function getContent()
    {
        return $this->getVarSet('content');
    }
    function getAPIRequired()
    {
        return $this->getVarSet('xenin');
    }

    function canTalk($obj)
    {
        $this->xenins = $obj->getContent();
        $vars = $obj->getContentNames();
        $ins = $this->getVarSetNames("xenin");
        $talk = true;
        foreach ($ins as $in)
        {
            if(!in_array($in,$vars))
            {
                $talk = false;
                break;
            }
        }
        return $talk;
    }

    function posttoregister($op, $id=0)
    {
        include_once 'modules/xen/xarclasses/xenquery.php';

        $tablefields = array();
        $fields = $this->toArray();
        foreach ($fields as $key => $value) {
            $tablefields[] = array('name' => 'xen_' . $key,   'value' => $value);
        }

        switch ($op) :
            case "create" :
                $query = new xenQuery("INSERT",$this->registertable);
                $query->addfields($tablefields);
                break ;
            case "update" :
                $query = new xenQuery("UPDATE",$this->registertable);
                $query->addfields($tablefields);
                $query->eq('xen_id',$id);
                break ;
            case "delete" :
                $query = new xenQuery("DELETE",$this->registertable);
                $query->eq('xen_id',$id);
                break ;
        endswitch ;
        if (!$query->run()) return false;
        return true;
    }

    function create()
    {
        return $this->posttoregister('create');
    }

    function modify($id)
    {
        return $this->posttoregister('modify',$id);
    }

//    function delete($id)
//    {
//        return $this->posttoregister('delete',$id);
//    }

    function getID()
    {
        return $this->register_id;
    }

    function getName()
    {
        return $this->register_name;
    }

//---------------------------------------------------------
// Private Functions
//---------------------------------------------------------
    function createVarSet($prefix,$args)
    {
//        foreach ($args as $arg) ${$prefix}_{$arg} = null;
    }
    function getVarSetNames($str)
    {
/*
        $keys =  array_keys(get_class_vars(get_class($this)));
        $names = array();
        $len = strlen($str)+1;
        foreach ($keys as $key) {
            if(substr($key, 0, $len) == $str . '_') $names[] = substr($key,$len);
        }
        return $names;
*/
        $interface = $this->xenschema[$str];
        $names= array();
        foreach($interface['definition'] as $line) $names[] = $line['in'];
        return $lines;
    }

    function getVarSet($str) {
/*
        $vars = get_object_vars($this);
        $vararray = array();
        $len = strlen($str)+1;
        foreach ($vars as $key =>$value) {
            if(substr($key, 0, $len) == $str .'_') $vararray[substr($key,$len)] = $value;
        }
        return $vararray;
*/
        $keys =  array_keys(get_class_vars(get_class($this)));
        $values= array();
        foreach($this->xenschema['fields'] as $line) {
            $name = $line['in'];
            if (in_array($name,$keys)) {
                $out = $line['out'];
                $values[$out] = $this->{$name};
            }
            else {
                $msg = xarML('Did not find a variable #(1) as required by the schema',$name);
            echo $msg;exit;
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                xarErrorRender('template');
                return;
            }
        }
        return $values;
    }

    function setVarSet($str,$array) {
/*
        $vars = $this->getVarSetNames($str);
        foreach ($vars as $var) {
        if(isset($array[$var]))
            $this->{$str . '_' . $var} = $array[$var];
        }
*/
        $vars = $this->getVarNames($str);
        foreach ($vars as $var) {
        if(isset($array[$var]))
            $this->{$var} = $array[$var];
        }
    }

    function loadschema($schema) {
        $this->xenschema = $schema;
    }

    function getobj() {
        $schemaname = "xenstorageschema_{$this->view}";
        $this->xenschema[] = $schema;
    }

    function setquery($x='')
    {
        if ($x == '') $this->xenquery = array();
        else $this->xenquery = array($x);
    }
    function addquery($x)
    {
        $this->xenquery[] = $x;
    }
    function getquery($i=0)
    {
        return $this->xenquery[$i];
    }
    function getInstance($id,$view='full')
    {
        $r = new xenStorageReader($this,$view,'SELECT');
        $templates = $r->templates;
        foreach ($templates as $t) {
            $q =& $t->query;
            $q->eq($t->getitemindex(),$id);
            $t->transferin();
        }
        return true;
    }
    function save($id,$view='full')
    {
        $r = new xenStorageReader($this,$view,'UPDATE');
        $templates = $r->templates;
        foreach ($templates as $t) {
            $q =& $t->query;
            $t->transferout($id);
        }
        return true;
    }
    function add($id=0,$view='full')
    {
        $r = new xenStorageReader($this,$view,'INSERT');
        $templates = $r->templates;
        foreach ($templates as $t) {
            $q =& $t->query;
            $t->transferout($id);
        }
        return true;
    }
    function delete($id,$view='full')
    {
        $r = new xenStorageReader($this,$view,'DELETE');
        $templates = $r->templates;
        foreach ($templates as $t) {
            $q =& $t->query;
            $q->eq($t->getitemindex(),$id);
            $q->run();
        }
        return true;
    }
 }
?>