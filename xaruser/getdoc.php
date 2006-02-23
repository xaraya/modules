<?php
/**
 * Get a doc
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @author Release module development team
 */
/**
 * Add an extension and request an ID
 *
 * @param enum phase Phase we are at
 * 
 * @return array
 * @author Release module development team
 */
 
function release_user_getdoc()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $rdid = xarVarCleanFromInput('rdid');

    // The user API function is called.
    $item = xarModAPIFunc('release',
                          'user',
                          'getdoc',
                          array('rdid' => $rdid));

    if ($item == false) return;

        $hooks = xarModCallHooks('item',
                                 'display',
                                 $rdid,
                                 array('itemtype'  => '3',
                                       'returnurl' => xarModURL('release',
                                                                'user',
                                                                'getdoc',
                                                                 array('rdid' => $rdid))
                                             )
                                        );

    if (empty($hooks)) {
        $item['hooks'] = '';
    } elseif (is_array($hooks)) {
        $item['hooks'] = join('',$hooks);
    } else {
        $item['hooks'] = $hooks;
    }

    $item['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
    $item['title'] = xarVarPrepHTMLDisplay($item['title']);

    return $item;
}

?>