<?php

/**
 * File: $Id$
 *
 * Create new repository entry
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/



/**
 * add new item
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 */
function bkview_admin_new()
{
    // Security check
    if (!xarSecurityCheck('AdminAllRepositories')) return;
    
    // Generate the items which need to be in the form
    $data['authid'] = xarSecGenAuthKey();
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Add repository'));
    $data['reponame'] = xarVarPrepForDisplay(xarML('<untitled>'));
    $data['repopath'] = xarVarPrepForDisplay(xarML('/var/bk/repo'));
    $item = array();
    $item['module'] = 'bkview';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['pageinfo']=xarML('Register a new repository');
    return $data;
}

?>