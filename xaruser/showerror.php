<?php

/**
 * Show a xarBB error message
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author Jason Judge
*/

function xarbb_user_showerror($args)
{
    extract($args);
    
    xarVarFetch('errortype', 'str:1', $errortype, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('tid', 'id', $tid, 0, XARVAR_NOT_REQUIRED);

    $data = compact('errortype', 'tid');

    return $data;
}

?>