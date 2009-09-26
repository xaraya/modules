<?php
/**
 * Status report for the current translation
 *
 * @package modules
 * @subpackage translations
 * @copyright (C) 2004 Marcel van der Boom
 * @link http://www.xaraya.com
 * 
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_user_show_status()
{
    return xarMod::guiFunc('translations', 'admin', 'show_status');
}
?>