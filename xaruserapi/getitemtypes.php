<?php
/**
 * Utility function to retrieve the list of item types of this module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * utility function to retrieve the list of item types of this module
 *
 * @return array containing the item types and their description
 */
function release_userapi_getitemtypes($args)
{
    $itemtypes = [];

    // Use the extension type as itemtypes
    $exttypes = xarMod::apiFunc('release', 'user', 'getexttypes');

    foreach ($exttypes as $etype=>$ename) {
        if ($etype != 0) {
            $itemtypevalue = $etype;
            $itemtypes[$etype] = ['label' => $ename,
                                   'title' => xarML('Extension Type'),
                                   'url' => xarController::URL('release', 'user', 'display', ['type' => $etype]), ];
        }
    }
    return $itemtypes;
}
