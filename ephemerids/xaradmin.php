<?php
/**
 * File: $Id$
 * 
 * Ephemerids
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Ephemerids Module
 * @author Volodymyr Metenchuk
*/

function ephemerids_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;

    // we only really need to show the default view (overview in this case)
    if (xarModGetVar('adminpanels', 'overview') == 0){
        return array();
    } else {
        xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));
    }
    // success
    return true;
}

/**
 * Default
 */
function ephemerids_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('AddEphemerids')) return;

    // TODO: figure out how to get a list of *available* languages

    $data['authid'] = xarSecGenAuthKey();

    return $data;
}

/**
 * Generate Ephemerids listing for display
 */
function ephemerids_admin_view()
{
    // Get parameters from whatever input we need
    $startnum = xarVarCleanFromInput('startnum');
    $data['items'] = array();

    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;

    // Specify some labels for display
    $data['daylabel'] = xarVarPrepForDisplay(xarML('Day'));
    $data['monthlabel'] = xarVarPrepForDisplay(xarML('Month'));
    $data['yearlabel'] = xarVarPrepForDisplay(xarML('Year'));
    $data['eventlabel'] = xarVarPrepForDisplay(xarML('Event'));
    $data['languagelabel'] = xarVarPrepForDisplay(xarML('Language'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('ephemerids', 'user', 'countitems'),
                                    xarModURL('ephemerids', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('ephemerids', 'itemsperpage'));

    // The admin API function is called. 
    $ephemlist = xarModAPIFunc('ephemerids',
                               'admin',
                               'display');
    // Check for exceptions
    if (!isset($ephemlist) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($ephemlist); $i++) {
        $ephem1 = $ephemlist[$i];
        if (xarSecurityCheck('EditEphemerids',0)) {
            $ephemlist[$i]['editurl'] = xarModURL('ephemerids',
                                             'admin',
                                             'modify',
                                             array('eid' => $ephem1['eid']));
        } else {
            $ephemlist[$i]['editurl'] = '';
        }
        $ephemlist[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteEphemerids',0)) {
            $ephemlist[$i]['deleteurl'] = xarModURL('ephemerids',
                                               'admin',
                                               'delete',
                                               array('eid' => $ephem1['eid']));
        } else {
            $ephemlist[$i]['deleteurl'] = '';
        }
        $ephemlist[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['ephemlist'] = $ephemlist;

    // Return the template variables defined in this function
    return $data;
}

/**
 * Add new ephemerids to database.
 */
function ephemerids_admin_add()
{
    list($did, 
         $mid, 
         $yid, 
         $content, 
         $elanguage) = xarVarCleanFromInput('did',
                                            'mid', 
                                            'yid', 
                                            'content', 
                                            'elanguage');

    if (!empty($elanguage)) {
        $elanguage = 'ALL';
    }

    // Confirm Auth
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AddEphemerids')) return;

    // The API function is called.  
    $emp = xarModAPIFunc('ephemerids',
                         'admin',
                         'add',
                         array('did' => $did, 
                               'mid' => $mid, 
                               'yid' => $yid, 
                               'content' => $content, 
                               'elanguage' => $elanguage));

    // The return value of the function is checked here
    if (!isset($emp) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));

    // Return
    return true;
}

/**
 * Delete selected ephemerids
 */
function ephemerids_admin_delete($args)
{
    list($eid, 
         $confirmation,
         $confirm) = xarVarCleanFromInput('eid',
                                          'confirmation',
                                          'confirm');

    extract ($args);

    // Security Check
    if(!xarSecurityCheck('DeleteEphemerids')) return;

    // Check for confirmation.
    if (empty($confirmation)) {
    $data['eid'] = $eid;
    $data['authid'] = xarSecGenAuthKey();

    return $data;

    }

    if (!xarSecConfirmAuthKey()) return;

    // The API function is called
    if (xarModAPIFunc('ephemerids',
                      'admin',
                      'delete',
                      array('eid' => $eid))) {

    }

    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));

    // Return
    return true;
}

function ephemerids_admin_modify($args)
{
    // Get parameters
    list($eid,
         $objectid)= xarVarCleanFromInput('eid',
                                         'objectid');

    extract($args);

    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  
    if (!empty($objectid)) {
        $eid = $objectid;
    }

    // The user API function is called. 
    $data = xarModAPIFunc('ephemerids',
                         'user',
                         'get',
                         array('eid' => $eid));

    if ($data == false) return;

    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;

    // Get menu variables 
    
    $hooks = xarModCallHooks('item','modify',$eid,$data);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    
    $data['authid'] = xarSecGenAuthKey();
    
    // Return the template variables defined in this function
    return $data;
}


function ephemerids_admin_update($args)
{
    // Get parameters
    list($did, 
         $mid, 
         $yid, 
         $content, 
         $elanguage,
         $eid,
         $objectid) = xarVarCleanFromInput('did',
                                           'mid', 
                                           'yid', 
                                           'content', 
                                           'elanguage',
                                           'eid',
                                           'objectid');

    extract($args);

    if (!empty($objectid)) {
        $eid = $objectid;
    }

    if (!empty($elanguage)) {
        $elanguage = 'All';
    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called.
    if(!xarModAPIFunc('ephemerids',
                    'admin',
                    'update',
                    array('eid' => $eid,
                          'did' => $did,
                          'mid' => $mid,
                          'yid' => $yid,
                          'content' => $content,
                          'elanguage' => $elanguage))) {
        return; // throw back
    }

    //Redirect
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));

    // Return
    return true;
}

/**
 * modify configuration
 */
function ephemerids_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminEphemerids')) return;

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

/**
 * update configuration
 */
function ephemerids_admin_updateconfig()
{
    $itemsperpage = xarVarCleanFromInput('itemsperpage');

    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AdminEphemerids')) return;

    if (!isset($itemsperpage)) {
        $itemsperpage = 10;
    }
    xarModSetVar('ephemerids', 'itemsperpage', $itemsperpage);

    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'modifyconfig'));

    return true;
}

?>