<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Add new html tag
 *
 * @public
 * @author Richard Cave 
 */
function html_admin_newtype()
{
    // Security Check
    if(!xarSecurityCheck('AddHTML')) return;
    
    $data['authid'] = xarSecGenAuthKey();
    $data['createbutton'] = xarML('Create Tag Type');

    // Include 'formcheck' JavaScript.
    // TODO: move this to a template widget when available.
    xarModAPIfunc(
        'base', 'javascript', 'modulefile',
        array('module'=>'base', 'filename'=>'formcheck.js')
    );

    // Return the output
    return $data;
}

?>
