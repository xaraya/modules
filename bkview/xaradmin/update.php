<?php


/**
 * File: $Id$
 *
 * Update a repository entry
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * This is a standard function that is called with the results of the
 * form supplied by bkview_admin_modify() to update a current item
 * @param 'exid' the id of the item to be updated
 * @param 'name' the name of the item to be updated
 * @param 'number' the number of the item to be updated
 */
function bkview_admin_update($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('reponame','str::',$reponame);
    xarVarFetch('repopath','str::',$repopath);
    extract($args);
    
    if (!xarSecConfirmAuthKey()) return;
    
    if(!xarModAPIFunc('bkview','admin','update',array('repoid' => $repoid,
                                                      'reponame' => $reponame,
                                                      'repopath' => $repopath))) {
        return; // throw back
    }
    xarSessionSetVar('statusmsg', xarMLByKey('_BKVIEW_UPDATED'));
    xarResponseRedirect(xarModURL('bkview', 'admin', 'view'));
    return true;
}
?>