<?php
sys::import('modules.dynamicdata.class.objects.base');

class RegisteredUser extends DataObject
{

    public $dbconn;
    public $rolestable;

    public function __construct(DataObjectDescriptor $dataobject)
    {
        parent::__construct($dataobject);
        $this->dbconn = xarDB::getConn();
        xarModAPILoad('roles');
        $xartable = xarDB::getTables();
        $this->rolestable = $xartable['roles'];
    }

    public function createItem(Array $data = array())
    {
        //TODO: move this into the user property?
        // Confirm that this group or user does not already exist
        $q = new xarQuery('SELECT',$this->rolestable);
        if (empty($data['uname'])) $data['uname'] = $this->properties['user_name']->value;
        $q->eq('uname',$data['uname']);
        if (!$q->run()) return;

        if ($q->getrows() > 0) {
            throw new DuplicateException(array('role',$data['uname']));
        }

        $id = parent::createItem($data);

        // Set the email useage for this user to false
        xarModItemVars::set('roles','allowemail', false, $id);

        // Add the user to a group
        $parentid = xarModVars::get('registration', 'defaultgroup');
        if (!empty($parentid)) {
            $parent = xarRoles::get($parentid);
            $child = xarRoles::get($id);
            if (!$parent->addMember($child))
                throw new Exception('Unable to create a roles relation');
        }

        // add the duvs
        if (!xarVarFetch('duvs','array',$duvs,array(),XARVAR_NOT_REQUIRED)) return;
        foreach($duvs as $key => $value) {
            xarModItemVars::set('roles',$key, $value, $id);
        }

        // Let's finish by sending emails to those that require it based on options - the user or the admin
        // and redirecting to appropriate pages that depend on user state and options set in the registration config
        // note: dont email password if user chose his own (should this condition be in the createnotify api instead?)

        $emailargs = $this->getFieldValues();
        $emailargs['password'] = xarModVars::get('registration', 'chooseownpassword') ? '' : $this->properties['password']->value;
        $emailargs['emailvalues'] = $emailargs;
        $ret = xarModAPIFunc('registration','user','createnotify',$emailargs);
        if (!$ret) return;


        // Let any hooks know that we have created a new user.
        $item['module'] = 'roles';
        $item['itemtype'] = $this->properties['itemtype']->value;
        $item['itemid'] = $id;
        xarModCallHooks('item', 'create', $id, $item);

        return $id;
    }

    public function updateItem(Array $data = array())
    {
        return parent::updateItem($data);
    }
}
?>
