<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * View shouts for moderation
 *
 * @return array
 */
function shouter_admin_view()
{
    if (!xarSecurityCheck('EditShouter')) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, xarModGetVar('shouter', 'itemsperpage'), XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('shouter', 'admin', 'menu');

    $data['items'] = array();

    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('shouter', 'user', 'countitems'),
        xarModURL('shouter', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('shouter', 'itemsperpage'));

    $items = xarModAPIFunc('shouter', 'user', 'getall',
                     array('startnum' => $startnum,
                           'numitems' => $numitems
                     ));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        if (xarSecurityCheck('EditShouter', 0, 'Item', "$item[name]:All:$item[shoutid]")) {
            $items[$i]['editurl'] = xarModURL('shouter', 'admin', 'modify',
                                        array('shoutid' => $item['shoutid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');

        if (xarSecurityCheck('DeleteShouter', 0, 'Item', "$item[name]:All:$item[shoutid]")) {
            $items[$i]['deleteurl'] = xarModURL('shouter', 'admin', 'delete',
                                          array('shoutid' => $item['shoutid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');

        if (xarSecurityCheck('DeleteAllShouter', 0, 'All', "$item[name]:All:$item[shoutid]")) {
            $items[$i]['deleteallurl'] = xarModURL('shouter', 'admin', 'deleteall');
        } else {
            $items[$i]['deleteallurl'] = '';
        }
        $items[$i]['deletealltitle'] = xarML('Delete All');

    }

    $data['items'] = $items;

    return $data;
}
?>