<?php
/**
 * The main administration function
 *
 * @package Xaraya 
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage SiteContact Module
 * @copyright (C) 2004-2009 2skies.com
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