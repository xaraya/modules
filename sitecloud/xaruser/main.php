<?php 
/**
 * File: $Id$
 * 
 * Xaraya sitecloud
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage sitecloud Module
 * @author John Cox
*/

function sitecloud_user_main()
{
    xarVarFetch('startnum', 'id', $startnum, '1', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('catid', 'str:0:', $catid, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);

    // Security Check
    if(!xarSecurityCheck('Overviewsitecloud')) return;

    $data = array();

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // The user API function is called
    $links = xarModAPIFunc('sitecloud',
                           'user',
                           'getall',
                           array('catid' => $catid,
                                  'startnum' => $startnum,
                                  'numitems' => xarModGetVar('sitecloud', 'itemsperpage')));

    if (isset($links['catid'])) {
        $data['catid'] = $links['catid'];
    } else {
        $data['catid'] = '';
    }

    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        //$link['udated'] = time() - $link['date'];
        $links[$i]['updated'] = trim(xarLocaleFormatDate("%a, %d %b %Y %H:%M:%S %Z",($link['date'])));
        $links[$i]['when']    = time() - $link['date'];
    }

    $data['links'] = $links;
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('sitecloud', 'user', 'countitems'),
                                    xarModURL('sitecloud', 'user', 'main', array('startnum' => '%%')),
                                    xarModGetVar('sitecloud', 'itemsperpage'));
    
    return $data;
}
?>