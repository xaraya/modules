<?php

/**
 * File: $Id$
 *
 * contains the user funtions used for interacting with
 * the database as well as setting user options, etc.
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/

/**
 * Comments API
 * @package Xaraya
 * @subpackage Comments_API
 */

include_once('modules/comments/xarincludes/defines.php');


/**
 * Sets up any formaction / formdisplay hooks
 *
 */
function comments_userapi_formhooks()
{

    $hooks = array();
    $hooks['formaction']              = xarModCallHooks('item', 'formaction', '', array(), 'comments');
    $hooks['formdisplay']             = xarModCallHooks('item', 'formdisplay', '', array(), 'comments');

    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('',$hooks['formaction']);
    }

    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('',$hooks['formdisplay']);
    }

    return $hooks;
}

?>