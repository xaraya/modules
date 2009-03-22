<?php
/**
 * The main administration function
 *
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * The main administration function
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
function sitecontact_admin_main()
{
    if (!xarSecurityCheck('EditSiteContact',1)) return;

    xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes',array('action'=>'view')));

    /* success */
    return true;
}
?>