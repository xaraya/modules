<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * Overview function that displays the standard Overview page
 */
function messages_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminMessages',0)) return;

    $data=array();

     return xarTplModule('messages', 'admin', 'main', $data,'main');
}

?>
