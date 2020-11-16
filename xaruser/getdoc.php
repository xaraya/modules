<?php
/**
 * Get a doc
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
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
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    $rdid = xarVarCleanFromInput('rdid');

    // The user API function is called.
    $item = xarMod::apiFunc(
        'release',
        'user',
        'getdoc',
        array('rdid' => $rdid)
    );

    if ($item == false) {
        return;
    }

    $hooks = xarModHooks::call(
        'item',
        'display',
        $rdid,
        array('itemtype'  => '3',
                                       'returnurl' => xarController::URL(
                                           'release',
                                           'user',
                                           'getdoc',
                                           array('rdid' => $rdid)
                                       )
                                             )
    );

    if (empty($hooks)) {
        $item['hooks'] = '';
    } elseif (is_array($hooks)) {
        $item['hooks'] = join('', $hooks);
    } else {
        $item['hooks'] = $hooks;
    }

    $item['docsf'] = nl2br(xarVar::prepHTMLDisplay($item['docs']));
    $item['title'] = xarVar::prepHTMLDisplay($item['title']);

    return $item;
}
