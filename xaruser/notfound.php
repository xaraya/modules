<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
 
function content_user_notfound()
{
    if (!xarVarFetch('msg', 'str', $msg, NULL, XARVAR_NOT_REQUIRED)) return;
    return xarTplModule('base','message','notfound',array('msg' => $msg));
}

?>