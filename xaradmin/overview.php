<?php
/**
 * Overview displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */
/**
 * Overview displays standard Overview page
 *
 * @returns array xarTplModule with $data containing template data
 * @return array containing the menulinks for the overview item on the main manu
 * @since 15 Oct 2005
 */
function xarlinkme_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AddxarLinkMe',0)) return;

    $data=array();
    $data = xarModAPIFunc('xarlinkme', 'admin', 'menu');  
    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview 
     */

    return xarTplModule('xarlinkme', 'admin', 'main', $data,'main');
}

?>
