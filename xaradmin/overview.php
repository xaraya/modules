<?php
/**
 * Overview menu link
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * The displays the overview in the menu
 * @author Jo Dalle Nogare
 */
function sitecontact_admin_overview()
{
    if (!xarSecurityCheck('AdminSiteContact')) return;

    $data=array();
    
    /* let's just display the main module overview */
    return xarTplModule('sitecontact', 'admin', 'main',$data,'main');
}

?>