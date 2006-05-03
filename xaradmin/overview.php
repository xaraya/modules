<?php
/**
 * Standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * Overview function that displays the standard Overview page
 */
function ephemerids_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminEphemerids',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('ephemerids', 'admin', 'main', $data,'main');
}

?>
