<?php
/**
 * Xaraya Autolinks Overview
 *
 * @package modules
 * @copyright (C) 2002-2006 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Autolinks Module
 * @link http://xaraya.com/index.php/release/11.html
 * @author Jason Judge/Jim McDonald
*/
/**
 * Overview displays standard Overview page
 */
function autolinks_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('EditAutolinks',0)) return;

    $data=array();
    
    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview 
     */

    return xarTplModule('autolinks', 'admin', 'main', $data,'main');
}

?>