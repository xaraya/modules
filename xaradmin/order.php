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
 * Function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * What this function does
 *
 * @return array
 */
function crispbb_admin_order($args)
{
    if (!xarSecurityCheck('AdminCrispBB', 0)) {
         return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));
    }
    if (!xarVarFetch('fid', 'int:1', $itemid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid', 'int:1', $itemid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid', 'int:1', $catid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('direction', 'pre:trim:lower:enum:up:down', $direction, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'pre:trim:lower:str:1', $return_url, '', XARVAR_NOT_REQUIRED)) return;

    if (empty($itemid)) $invalid[] = 'itemid';
    if (empty($catid)) $invalid[] = 'catid';
    if (empty($direction)) $invalid[] = 'direction';
    if (!empty($invalid)) {
        $msg = 'Invalid #(1) for #(2) function #(3) in module #(4)';
        $vars = array(join(', ', $invalid), 'admin', 'order', 'crispBB');
        throw new BadParameterException($vars, $msg);
    }
    if (!xarSecConfirmAuthKey()) {
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
    }
    $forums = DataObjectMaster::getObjectList(array('name' => 'crispbb_forums'));
    $forums->setCategories($catid);
    $filter = array('sort' => 'forder ASC', 'catid' => $catid);
    $forums->getItems($filter);

    $fids = array_keys($forums->items);

    foreach ($fids as $i => $fid) {
        if ($fid == $itemid) {
            $oldorder = $forums->items[$itemid]['forder'];
            if (isset($fids[$i-1]) && $direction == 'up') {
                $swapid = $fids[$i-1];
                $neworder = $forums->items[$swapid]['forder'];
            } elseif (isset($fids[$i+1]) && $direction == 'down') {
                $swapid = $fids[$i+1];
                $neworder = $forums->items[$swapid]['forder'];
            }
            break;
        }
    }

    if (isset($oldorder) && isset($neworder)) {
        $object = DataObjectMaster::getObject(array('name' => 'crispbb_forums'));
        $fieldlist = array('forder');
        $object->setFieldlist($fieldlist);

        $object->getItem(array('itemid' => $itemid));
        $object->properties['forder']->setValue($neworder);
        $object->updateItem(array('itemid' => $itemid));

        $object->getItem(array('itemid' => $swapid));
        $object->properties['forder']->setValue($oldorder);
        $object->updateItem(array('itemid' => $swapid));
    }

    if (empty($return_url)) {
        $return_url = xarServer::getVar('HTTP_REFERER');
    }

    xarResponse::Redirect($return_url);
    return true;

}
?>