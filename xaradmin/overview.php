<?php
/**
 * Overview displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Overview displays standard Overview page
 *
 * @author jojodee
 * @returns array xarTplModule with $data containing template data
 * @return array containing the menulinks for the overview item on the main manu
 */
function legis_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminLegis',0)) return;

    $data=array();
    
    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview 
     */

    return xarTplModule('Legis', 'admin', 'main', $data,'main');
}

?>
