<?php
/**
 * File: $Id$
 * 
 * Xaraya Smilies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with smilies Menu information
 */
function smilies_admin_main()
{
    // Security Check
	if(!xarSecurityCheck('EditSmilies')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));
    }
    // success
    return true;
}

/**
 * add new forum
 */
function smilies_admin_new()
{   
    $phase = xarVarCleanFromInput('phase');    

    // Security Check
	if(!xarSecurityCheck('AddSmilies')) return;

    if (empty($phase)){
        $phase = 'form';
    }

    switch(strtolower($phase)) {

        case 'form':
        default:

            $data['authid'] = xarSecGenAuthKey();
            break;

        case 'update':

            list($code,
                 $icon,
                 $emotion) = xarVarCleanFromInput('code',
                                                'icon',
                                                'emotion');

            // Check arguments
            if (empty($code)) {
                $msg = xarML('No Smiley Code Entered');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }
            if (empty($icon)) {
                $msg = xarML('No Icon Entered');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }
            if (empty($icon)) {
                $msg = xarML('No Emotion Entered');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }

            // The API function is called
            if (!xarModAPIFunc('smilies',
                               'admin',
                               'create',
                               array('code' => $code,
                                     'icon' => $icon,
                                     'emotion' => $emotion))) return;

            xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));

            break;
     
    }

    // Return the output
    return $data;
}

function smilies_admin_delete()
{
    // Get parameters
    list($sid,
         $confirmation) = xarVarCleanFromInput('sid',
                                              'confirmation');

    // The user API function is called.
    $data = xarModAPIFunc('smilies',
                          'user',
                          'get',
                          array('sid' => $sid));

    if ($data == false) return;

    // Security Check
	if(!xarSecurityCheck('DeleteSmilies')) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Remove User From Group.
    if (!xarModAPIFunc('smilies',
		               'admin',
		               'delete', 
                        array('sid' => $sid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));

    // Return
    return true;
}

function smilies_admin_modify()
{
    list($sid,
         $phase) = xarVarCleanFromInput('sid',
                                        'phase');

    if (empty($phase)){
        $phase = 'form';
    }

    switch(strtolower($phase)) {

        case 'form':
        default:
            // The user API function is called.
            $data = xarModAPIFunc('smilies',
                                  'user',
                                  'get',
                                  array('sid' => $sid));

            if ($data == false) return;

            // Security Check
            if(!xarSecurityCheck('EditSmilies')) return;

            //Load Template
            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'update':
            // Get parameters
            list($sid,
                 $code,
                 $icon,
                 $emotion) = xarVarCleanFromInput('sid',
                                                  'code',
                                                  'icon',
                                                  'emotion');

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            // The API function is called.
            if(!xarModAPIFunc('smilies',
                              'admin',
                              'update',
                               array('sid'      => $sid,
                                     'code'     => $code,
                                     'icon'     => $icon,
                                     'emotion'  => $emotion))) return;

            // Redirect
            xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));

            break;
    }
    
	return $data;

}

function smilies_admin_view()
{

    // Get parameters from whatever input we need
    $startnum = xarVarCleanFromInput('startnum');
    $data['items'] = array();

    // Specify some labels for display
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('smilies', 'user', 'countitems'),
                                    xarModURL('smilies', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('smilies', 'itemsperpage'));
    // Security Check
	if(!xarSecurityCheck('EditSmilies')) return;

    // The user API function is called
    $links = xarModAPIFunc('smilies',
                           'user',
                           'getall',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('smilies',
                                                            'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('There are no smilies registered');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditSmilies',0)) {
            $links[$i]['editurl'] = xarModURL('smilies',
                                              'admin',
                                              'modify',
                                              array('sid' => $link['sid']));
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteSmilies',0)) {
            $links[$i]['deleteurl'] = xarModURL('smilies',
                                               'admin',
                                               'delete',
                                               array('sid' => $link['sid']));
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['items'] = $links;

    // Return the template variables defined in this function
    return $data;
}

?>