<?php 
/**
 * File: $Id$
 * 
 * Xaraya Censor
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Censor Module
 * @author John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with censor Menu information
 */
function censor_admin_main()
{
    // Security check
    if (!xarSecAuthAction(0, 'censor::', '::', ACCESS_EDIT)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
    // Return the output
    return array();
}

/**
 * add new item
 */
function censor_admin_new()
{
    
    // Security check
    if (!xarSecAuthAction(0, 'censor::', '::', ACCESS_ADD)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
    
    $data['authid'] = xarSecGenAuthKey();

    // Return the output
    return $data;
}

/**
 * This is a standard function that is called with the results of the
 * form supplied by censor_admin_new() to create a new item
 * @param 'keyword' the keyword of the link to be created
 * @param 'title' the title of the link to be created
 * @param 'url' the url of the link to be created
 * @param 'comment' the comment of the link to be created
 */
function censor_admin_create($args)
{
    // Get parameters from whatever input we need
    $keyword = xarVarCleanFromInput('keyword');

    extract($args);

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new item',
                    'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // Check arguments
    if (empty($keyword)) {
        $msg = xarML('No Keyword Provided, Please Go Back and Provide censor Keyword');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Load API
    if (!xarModAPILoad('censor', 'admin')) return;

    // The API function is called
    $cid = xarModAPIFunc('censor',
                        'admin',
                        'create',
                        array('keyword' => $keyword));

    xarResponseRedirect(xarModURL('censor', 'admin', 'view'));

    // Return
    return true;
}

/**
 * modify an item
 * @param 'cid' the id of the link to be modified
 */
function censor_admin_modify($args)
{
    // Get parameters from whatever input we need
    list($cid,
         $obid)= xarVarCleanFromInput('cid',
                                     'obid');


    extract($args);

    if (!empty($obid)) {
        $cid = $obid;
    }

    // Load API
    if (!xarModAPILoad('censor', 'user')) return;

    $censor = xarModAPIFunc('censor',
                         'user',
                         'get',
                         array('cid' => $cid));

    if ($censor == false) return;

    // Security check
    if (!xarSecAuthAction(0, 'censor::Item', "$censor[keyword]::$cid", ACCESS_EDIT)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
    
    $censor['authid'] = xarSecGenAuthKey();
    return $censor;
    
}


/**
 * This is a standard function that is called with the results of the
 * form supplied by censor_admin_modify() to update a current item
 * @param 'cid' the id of the link to be updated
 * @param 'keyword' the keyword of the link to be updated
 * @param 'title' the title of the link to be updated
 * @param 'url' the url of the link to be updated
 * @param 'comment' the comment of the link to be updated
 */
function censor_admin_update($args)
{
    // Get parameters from whatever input we need
    list($cid,
         $obid,
         $keyword) = xarVarCleanFromInput('cid',
                                         'obid',
                                         'keyword');

    extract($args);

    if (!empty($obid)) {
        $cid = $onid;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new item',
                    'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // Load API
    if (!xarModAPILoad('censor', 'admin')) return;

    if (!xarModAPIFunc('censor',
                       'admin',
                       'update',
                       array('cid' => $cid,
                             'keyword' => $keyword))) return;

    xarResponseRedirect(xarModURL('censor', 'admin', 'view'));

    // Return
    return true;
}

/**
 * delete item
 * @param 'cid' the id of the item to be deleted
 * @param 'confirmation' confirmation that this item can be deleted
 */
function censor_admin_delete($args)
{
    // Get parameters from whatever input we need
    list($cid,
         $obid,
         $confirmation) = xarVarCleanFromInput('cid',
                                              'obid',
                                              'confirmation');
    extract($args);

     if (!empty($obid)) {
         $tid = $obid;
     }

    // Load API
    if (!xarModAPILoad('censor', 'user')) return;

    // The user API function is called
    $censor = xarModAPIFunc('censor',
                            'user',
                            'get',
                            array('cid' => $cid));

    if ($censor == false) return; 

    // Security check
    if (!xarSecAuthAction(0, 'censor::Item', "$censor[keyword]::$cid", ACCESS_DELETE)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Check for confirmation.
    if (empty($confirmation)) {

    $censor['authid'] = xarSecGenAuthKey();

    return $censor;

    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new item',
                    'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // Load API
    if (!xarModAPILoad('censor', 'admin')) return;

    // The API function is called
    if (!xarModAPIFunc('censor',
                       'admin',
                       'delete',
                       array('cid' => $cid))) return; 

    xarResponseRedirect(xarModURL('censor', 'admin', 'view'));

    // Return
    return true;
}

/**
 * view items
 */
function censor_admin_view()
{

    // Get parameters from whatever input we need
    $startnum = xarVarCleanFromInput('startnum');
    $data['items'] = array();

    // Specify some labels for display
    $data['keywordlabel'] = xarVarPrepForDisplay(xarMLByKey('Key Word'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarMLByKey('Options'));
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = '';

    if (!xarSecAuthAction(0, 'censor::', '::', ACCESS_EDIT)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Load API
    if (!xarModAPILoad('censor', 'user')) return;

    // The user API function is called
    $censors = xarModAPIFunc('censor',
                             'user',
                             'getall',
                             array('startnum' => $startnum,
                                   'numitems' => xarModGetVar('censor', 'itemsperpage')));

    if (empty($censors)) {
        $msg = xarML('No censor in database.',
                    'censor');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($censors); $i++) {
        $censor = $censors[$i];
        if (xarSecAuthAction(0, 'censor::', "$censor[keyword]::$censor[cid]", ACCESS_EDIT)) {
            $censors[$i]['editurl'] = xarModURL('censor',
                                             'admin',
                                             'modify',
                                             array('cid' => $censor['cid']));
        } else {
            $censors[$i]['editurl'] = '';
        }
        $censors[$i]['edittitle'] = xarML('Edit');
        if (xarSecAuthAction(0, 'censor::', "$censor[keyword]::$censor[cid]", ACCESS_DELETE)) {
            $censors[$i]['deleteurl'] = xarModURL('censor',
                                               'admin',
                                               'delete',
                                               array('cid' => $censor['cid']));
        } else {
            $censors[$i]['deleteurl'] = '';
        }
        $censors[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['items'] = $censors;

    // TODO : add a pager (once it exists in BL)
    $data['pager'] = '';

    // Return the template variables defined in this function
    return $data;
}

/**
 * modify configuration
 */
function censor_admin_modifyconfig()
{
    if (!xarSecAuthAction(0, 'censor::', '::', ACCESS_ADMIN)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

/**
 * update configuration
 */
function censor_admin_updateconfig()
{
    $replace= xarVarCleanFromInput('replace');

    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new item',
                    'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (!isset($replace)) {
        $replace = '*****';
    }
    xarModSetVar('censor', 'replace', $replace);

    xarResponseRedirect(xarModURL('censor', 'admin', 'modifyconfig'));

    return true;
}

?>