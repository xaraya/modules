<?php
/**
 * The main administration function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * The main administration function
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
function sitecontact_admin_main()
{
    if (!xarSecurityCheck('EditSiteContact',1)) return;

    xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes',array('action'=>'view')));

    /* success */
    return true;
}
?>