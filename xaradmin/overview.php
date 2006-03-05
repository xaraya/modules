<?php
/**
 * Overview displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * Overview displays standard Overview page
 *
 * Only used if you actually supply an overview link in your adminapi menulink function
 * and used to call the template that provides display of the overview
 *
 * @author the Stats module development team
 * @return array xarTplModule with $data containing template data
 * @since 5 March 2006
 */
function stats_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminStats',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTplModule('stats', 'admin', 'main', $data,'main');
}

?>
