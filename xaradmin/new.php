<?php

/**
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
    
    // First see in which step we got passed
    if(!xarVarFetch('step','int:1:3',$step,1)) return;
    if(!xarVarFetch('stepback','str:',$stepback, null, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('reponame',"str::",$reponame,'<'.xarML('untitled').'>')) return;
    $data['reponame'] = $reponame;
    if(!xarVarFetch('repotype','int:1:2',$repotype,1)) return;
    $data['repotype'] = $repotype;
    $data['branches'] = xarML('Not retrieved yet');
    if(!xarVarFetch('repopath',"str::",$repopath,$repotype==1 ? '/var/bk/repo' : '/var/mtn/repo.db')) return;
    $data['repopath'] = $repopath;
    switch ($step) {
        case 1: // Choosing repository type
            break;
        case 2: // Configuring the chosen type
            if(isset($stepback)) $step = 1;
            break;
        case 3: // Finishing up and adding the repo, if all goes well
            if(!xarVarFetch('mtfetch','str:',$mtfetch, null, XARVAR_NOT_REQUIRED)) return;
            if(isset($mtfetch)) {
                // Stay in the second phase, but retrieve the branches for the select
                $step = 2;
                if(!file_exists($repopath)) {
                    $data['branches'] = xarML('Database not found');
                } else {
                    $data['branches'] = array();
                }
            } elseif(isset($stepback)) {
                $step = 1;
            } else {
                // Add the repository with the data provided
                if(!xarVarFetch('repobranch','str:',$repobranch,'')) return;
                $args = array(
                            'repotype' => $repotype,
                            'reponame' => $reponame,
                            'repopath' => $repopath,
                            'repobranch' => $repobranch);
                $repoid = xarModAPIFunc('bkview','admin','create',array('reponame' => $reponame,'repopath' => $repopath, 'repotype' => $repotype, 'repobranch' => $repobranch));
                if (!isset($repoid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
                xarResponseRedirect(xarModURL('bkview', 'admin', 'view'));
                return true;                            
            }
            break;
    }
    if(!xarVarFetch('repopath',"str::",$repopath,$repotype==1 ? '/var/bk/repo' : '/var/mtn/repo.db')) return;
    $data['repopath'] = $repopath;
    extract($args);

    // Generate the items which need to be in the form
    $data['authid'] = xarSecGenAuthKey();
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Add repository'));
    
    
    $item = array();
    $item['module'] = 'bkview';
    $hooks = array();
    $hooks = xarModCallHooks('item','new','',$item);
    $data['hooks'] = $hooks;
    $data['pageinfo']=xarML('Register a new repository');
    $data['step'] = $step;
    
    return $data;
}

?>