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
function bkview_admin_new($args)
{
    // Security check
    if (!xarSecurityCheck('AdminAllRepositories')) return;
 
    if(!xarVarFetch('repopath',"str::",$repopath,'/var/bk/repo')) return;
    if(!xarVarFetch('reponame',"str::",$reponame,'<'.xarML('untitled').'>')) return;
    extract($args);

    // Generate the items which need to be in the form
    $data['authid'] = xarSecGenAuthKey();
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Add repository'));
    $data['reponame'] = $reponame;
    $data['repopath'] = $repopath;
    $item = array();
    $item['module'] = 'bkview';
    $hooks = array();
    $hooks = xarModCallHooks('item','new','',$item);
    $data['hooks'] = $hooks;
    $data['pageinfo']=xarML('Register a new repository');
    return $data;
}

?>