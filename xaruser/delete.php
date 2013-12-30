<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * delete item
 */
function publications_user_delete()
{
    if (!xarSecurityCheck('ModeratePublications')) return;

    $return = xarModURL('publications', 'user','view',array('ptid' => xarModVars::get('publications', 'defaultpubtype')));
    if(!xarVarFetch('confirmed',  'int', $confirmed,  NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemid',     'int', $itemid,     NULL,  XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('idlist',     'str', $idlist,     NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('returnurl',  'str', $returnurl,  $return,  XARVAR_NOT_REQUIRED)) {return;}

    if (!empty($itemid)) $idlist = $itemid;
    $ids = explode(',',trim($idlist,','));
    
    if (empty($idlist)) {
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarModURL('publications', 'user','view'));
        }
    }

    $data['message'] = '';
    $data['itemid']  = $itemid;

/*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $publication = DataObjectMaster::getObject(array('name' => 'publications_documents'));
    $access = DataPropertyMaster::getProperty(array('name' => 'access'));
    $nopermissionpage_id = xarModVars::get('publications', 'noprivspage');

    if (!isset($confirmed)) {
        $data['idlist'] = $idlist;
        if (count($ids) > 1) {
            $data['title'] = xarML("Delete Publications");
        } else {
            $data['title'] = xarML("Delete Publication");
        }
        $data['authid'] = xarSecGenAuthKey();
        $items = array();
        foreach ($ids as $i) {
            $publication->getItem(array('itemid' => $i));
            $item = $publication->getFieldValues();

# --------------------------------------------------------
#
# Are we allowed to delete the page(s)?
#
            $accessconstraints = xarMod::apiFunc('publications', 'admin', 'getpageaccessconstraints', array('property' => $publication->properties['access']));
            $allow = $access->check($accessconstraints['delete']);

            if (count($ids) == 1) {
                // If no access, then bail showing a forbidden or the "no permission" page or an empty page
                if (!$allow) {
                    if ($accessconstraints['delete']['failure']) return xarResponse::Forbidden();
                    elseif ($nopermissionpage_id) xarController::redirect(xarModURL('publications', 'user', 'display', array('itemid' => $nopermissionpage_id)));
                    else return xarTplModule('publications', 'user', 'empty');
                }
            } else {
                // More than one page to delete: just ignore the ones we can't
                continue;
            }

            $items[] = $item;
        }
        $data['items'] = $items;
        $data['yes_action'] = xarModURL('publications','user','delete',array('idlist' => $idlist));
        return xarTplModule('publications','user', 'delete',$data);        
    } else {
        if (!xarSecConfirmAuthKey()) return;
        
        foreach ($ids as $id) {
            $publication->getItem(array('itemid' => $i));

# --------------------------------------------------------
#
# Are we allowed to delete the page(s)?
#
            $accessconstraints = xarMod::apiFunc('publications', 'admin', 'getpageaccessconstraints', array('property' => $publication->properties['access']));
            $allow = $access->check($accessconstraints['delete']);

            if (count($ids) == 1) {
                // If no access, then bail showing a forbidden or the "no permission" page or an empty page
                if (!$allow) {
                    if ($accessconstraints['delete']['failure']) return xarResponse::Forbidden();
                    elseif ($nopermissionpage_id) xarController::redirect(xarModURL('publications', 'user', 'display', array('itemid' => $nopermissionpage_id)));
                    else return xarTplModule('publications', 'user', 'empty');
                }
            } else {
                // More than one page to delete: just ignore the ones we can't
                continue;
            }

            // Delete the publication
            $itemid = $publication->deleteItem(array('itemid' => $id));
            $data['message'] = "Publication deleted [ID $id]";

            // Inform the world via hooks
            $item = array('module' => 'publications', 'itemid' => $itemid, 'itemtype' => $publication->properties['itemtype']->value);
            xarHooks::notify('ItemDelete', $item);
        }
        
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarModURL('publications', 'user', 'view', $data));
        }
        return true;
    }
}

?>
