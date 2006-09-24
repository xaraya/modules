<?php
/**
 * Overview displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * Overview displays standard Overview page
 *
 * Only used if you actually supply an overview link in your adminapi menulink function
 * and used to call the template that provides display of the overview
 *
 * @returns array xarTplModule with $data containing template data
 * @since 2 Oct 2005
 */
function headlines_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminHeadlines',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTplModule('headlines', 'admin', 'main', $data,'main');
}

?>
