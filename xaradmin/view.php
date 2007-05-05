<?php
// Get the query abstraction
sys::import('modules.query.class.query');

function members_admin_view($args)
{
    if (!xarVarFetch('name', 'str:1:', $name, 'members_members')) return;
    if(!xarVarFetch('startnum', 'int:1', $data['startnum'], 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('order', 'str', $order, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarSecurityCheck('EditMembers')) return;

    //set outright for now until pager is working
    if (!isset($sort)) $sort = 'DESC';

    $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => $name));
    $whereclause = "roles_authmodid eq " . xarMod::getID('members') . " and roles_state ne " . ROLES_STATE_DELETED;
    $whereclause .= " and roles_type eq " . ROLES_USERTYPE;

    $module = xarModGetName();
    $regid = xarMod::getID($module);

    $items = $data['object']->getItems(array('where' => $whereclause));

    //TODO: this is not finished - we need to setup pager for any of the particular objects,
    //not just members table eg the category objects also use this function
    //
    $xartable = xarDB::getTables();
    $q = new Query('SELECT');
    //$q->addtable($xartable['roles'],'r');
    $q->addtable($xartable['members_members'],'m');
    //$q->join('r.id','m.id');
   $itemsperpage = xarModVars::get($module, 'itemsperpage');
    $q->setrowstodo($itemsperpage);
    $q->setstartat($data['startnum']);
    $q->setorder($order,$sort);
    //$q->qecho();
    if (!$q->run()) return;
    $data['total'] = $q->getrows();

  // get the records to be displayed
    $items = $q->output();
    $data['items'] = $items;

    // a bunch of params the pager will want to see in it's target url
    // maybe don't need all of them
    $data['params'] = array(
        'itemsperpage' => $itemsperpage,
        'startnum' => "%%",
    );
    //End of this start to the pager setup

    return $data;
}

?>