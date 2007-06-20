<?php
/**
 * Overview displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * Overview displays standard Overview page
 *
 * @return array xarTplModule with $data containing template data
            containing the menulinks for the overview item on the main manu
 * @since 14 Oct 2005
 */
function images_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminImages',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTplModule('images', 'admin', 'main', $data,'main');
}

?>