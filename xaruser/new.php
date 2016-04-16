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
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_user_new($args)
{
    // Xaraya security
    if (!xarSecurityCheck('ModeratePublications')) return;

    extract($args);

    // Get parameters
    if (!xarVarFetch('ptid',        'int',   $data['ptid'],       xarModVars::get('publications', 'defaultpubtype'),  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('catid',       'str',   $catid,      NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype',    'id',    $itemtype,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    
# --------------------------------------------------------
#
# Are we allowed to add a page?
#
    $accessconstraints = xarMod::apiFunc('publications', 'admin', 'getpageaccessconstraints', array('property' => $data['object']->properties['access']));
    $access = DataPropertyMaster::getProperty(array('name' => 'access'));
    $allow = $access->check($accessconstraints['add']);

    // If no access, then bail showing a forbidden or the "no permission" page or an empty page
    $nopermissionpage_id = xarModVars::get('publications', 'noprivspage');
    if (!$allow) {
        if ($accessconstraints['add']['failure']) return xarResponse::Forbidden();
        elseif ($nopermissionpage_id) xarController::redirect(xarModURL('publications', 'user', 'display', array('itemid' => $nopermissionpage_id)));
        else return xarTplModule('publications', 'user', 'empty');
    }
    
# --------------------------------------------------------
#
# Good to go. Continue
#
    $data['properties'] = $data['object']->getProperties();

    if (!empty($data['ptid'])) {
        $template = $pubtypeobject->properties['template']->value;
    } else {
// TODO: allow templates per category ?
       $template = null;
    }

    // Get the settings of the publication type we are using
    $data['settings'] = xarMod::apiFunc('publications','user','getsettings',array('ptid' => $data['ptid']));
    
    return xarTplModule('publications', 'user', 'new', $data, $template);
}

?>