<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @link http://xaraya.com/index.php/release/761.html  
 * @subpackage formantibot
 */
/**
 * Overview function that displays the standard Overview page
 *
 */
function formantibot_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('FormAntiBot-Admin',0)) return;

    $data=array();

    return xarTplModule('formantibot', 'admin', 'main', $data,'main');
}

?>