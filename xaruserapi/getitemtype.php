<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Utility function to retrieve an itemtype id
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return int id of the itemtype
 * @throws bad param
 */
function crispbb_userapi_getitemtype($args)
{
    extract($args);
    if (!isset($fid)) $fid = NULL;
    if (!isset($component)) $component = NULL;

    $itemtypes = xarMod::apiFunc('crispbb', 'user', 'getitemtypes',
        array('fid' => $fid, 'component' => $component));

    if (count($itemtypes) <> 1) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'itemtype', 'user', 'getitemtype', 'crispBB');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $typeids = array_keys($itemtypes);
    $itemtype = array_shift($typeids);

    return $itemtype;
}
?>