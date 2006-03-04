<?php
/**
 * Xaraya BBCode
 *
 * Based on pnBBCode Hook from larsneo
 * http://www.pncommunity.de
 * Converted to Xaraya by John Cox
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com

/**
 * Overview displays standard Overview page
 *
 * Only used if you actually supply an overview link in your adminapi menulink function
 * and used to call the template that provides display of the overview
 *
 * @return array xarTplModule with $data containing template data
 * @since 2 Oct 2005
 */
function bbcode_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('EditBBCode')) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTplModule('bbcode', 'admin', 'main', $data, 'main');
}

?>