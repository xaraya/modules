<?php
/*
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * view censored words
 */
function censor_admin_view()
{
    // Get parameters
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    // Specify some labels for display


    $data['authid'] = xarSecGenAuthKey();

    // ftb -> where we can set this?
    $data['selstyle']  = xarModGetUserVar('censor', 'selstyle');
    if (empty($data['selstyle'])){
        $data['selstyle'] = 'plain';
    }
    // select vars for drop-down menus
    $data['style']['plain']   = xarML('Plain');
    $data['style']['compact'] = xarML('Compact');
    $data['style']['icons'] = xarML('Icons');

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('censor', 'user', 'countitems'),
        xarModURL('censor', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('censor', 'itemsperpage'));
    // Security Check
    if (!xarSecurityCheck('EditCensor')) return;
    // The user API function is called
    $censors = xarModAPIFunc('censor',
                             'user',
                             'getall',
                              array('startnum' => $startnum,
                                    'numitems' => xarModGetVar('censor',
                                                               'itemsperpage')));
    if (empty($censors)) {
        return $data;

    }

    // Check individual permissions for Edit / Delete
    $data['items'] = array();
    for ($i = 0; $i < count($censors); $i++) {
        $censor = $censors[$i];
        if (xarSecurityCheck('EditCensor', 0)) {
            $censors[$i]['editurl'] = xarModURL('censor',
                'admin',
                'modify',
                array('cid' => $censor['cid']));
        } else {
            $censors[$i]['editurl'] = '';
        }
        $censors[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteCensor', 0)) {
            $censors[$i]['deleteurl'] = xarModURL('censor',
                                                  'admin',
                                                  'delete',
                                                  array('cid' => $censor['cid'],
                                                        'authid' => $data['authid']));

        } else {

            $censors[$i]['deleteurl'] = '';
        }

        $censors[$i]['locale'] = implode(", ", $censor['locale']);
        if ($censors[$i]['locale']== 'ALL') {
            $censors[$i]['locale']= xarML('All');
            }
    }
    // Add the array of items to the template variables
    $data['items'] = $censors;


    // Return the template variables defined in this function
    return $data;

}
?>
