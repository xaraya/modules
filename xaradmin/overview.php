<?php
/**
 * Overview displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Overview displays standard Overview page
 *
 * Only used if you actually supply an overview link in your adminapi menulink function
 * and used to call the template that provides display of the overview
 *
 * @author the ITSP module development team
 * @return array xarTplModule with $data containing template data
 * @since 3 Sept 2005
 */
function itsp_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminITSP',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTplModule('itsp', 'admin', 'main', $data,'main');
}

?>
