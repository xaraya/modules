<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Modify an Newsletter issue
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the issue to be modified
 * @param 'publication' publication id of the issue
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifyissue() 
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Get input parameters
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('display', 'str:1:', $display, 'unpublished')) return;
    if (!xarVarFetch('publication', 'int:0:', $publication, 0)) return;

    $issue = xarModAPIFunc('newsletter',
                           'user',
                           'getissue',
                           array('id' => $id));

    // Check for exceptions
    if (!isset($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Assign publication to issue pid if not set
    if ($publication == 0)
        $publication = $issue['pid'];
    
    // Get the chosen publication
    if ($publication != 0) {
        $pubItem = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $publication));

        // Check for exceptions
        if (!isset($pubItem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back

        // If issue fromname is empty, then set to publication fromname
        if (empty($issue['fromname'])) {
            $issue['fromname'] = $pubItem['fromname'];
        }
        // If issue fromemail is empty, then set to publication fromemail
        if (empty($issue['fromemail'])) {
            $issue['fromemail'] = $pubItem['fromemail'];
        }
    }

    // Get the list of publications
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                   array('phase' => 'publication'));
    
    // Check for exceptions
    if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    $issue['publications'] = $publications;

    // Get the list of owners
    $owners = xarModAPIFunc('newsletter',
                            'user',
                            'get',
                            array('phase' => 'owner'));

    $issue['owners'] = $owners;

    // Set hook variables
    $issue['module'] = 'newsletter';
    $hooks = xarModCallHooks('issue','modify',$id,$issue);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }

    // Get the admin menu
    $menu = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Set the template variables defined in this function
    $templateVarArray = array('authid' => xarSecGenAuthKey(),
        'updatebutton' => xarVarPrepForDisplay(xarML('Update Issue')),
        'hooks' => $hooks,
        'itemsperpage' => xarModGetVar('newsletter', 'itemsperpage'),
        'menu' => $menu,
        'display' => $display,
        'publication' => $publication,
        'issue' => $issue);

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>
