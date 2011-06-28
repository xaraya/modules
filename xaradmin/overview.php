<?php
/**
 * Display module overview
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Overview displays standard Overview page
 *
 * @returns array xarTplModule with $data containing template data
 * @return array containing the menulinks for the overview item on the main manu
 * @since 14 Oct 2005
 */
function content_admin_overview()
{
   /* Security Check */
       if (!xarSecurityCheck('EditContent',1)) {
        return;
    }
     
    if(!xarVarFetch('tab',   'str', $data['tab'],   '', XARVAR_NOT_REQUIRED)) {return;}

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTplModule('content','admin','overview',$data);
}

?>