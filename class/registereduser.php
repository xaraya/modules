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
        sys::import('xaraya.structures.query');
        $q = new Query('SELECT',$this->rolestable);
        if (empty($data['uname'])) $data['uname'] = $this->properties['user_name']->value;
        $q->eq('uname',$data['uname']);
        if (!$q->run()) return;

        if ($q->getrows() > 0) {
            throw new DuplicateException(array('role',$data['uname']));
        }
        // if the user didn't choose their own password we need to get it before it gets encrypted
        // so we can include it in the email we're going to send them 
        if (!xarModVars::get('registration', 'chooseownpassword')) {
            $password = $this->properties['password']->value;
            // and then call the setValue method so it gets hashed 
            $this->properties['password']->setValue($password);
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
        $emailargs['password'] = !empty($password) ? $password : '';
        $emailvalues = $emailargs;
        $emailargs['emailvalues'] = $emailvalues;
        $ret = xarMod::apiFunc('registration','user','createnotify',$emailargs);
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
