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

function publications_admin_new($args)
{
    if (!xarSecurity::check('AddPublications')) {
        return;
    }

    extract($args);

    // Get parameters
    if (!xarVar::fetch('ptid', 'id', $data['ptid'], null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('catid', 'str', $catid, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemtype', 'id', $itemtype, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (null === $data['ptid']) {
        $data['ptid'] = xarSession::getVar('publications_current_pubtype');
        if (empty($data['ptid'])) {
            $data['ptid'] = xarModVars::get('publications', 'defaultpubtype');
        }
    }
    xarSession::setVar('publications_current_pubtype', $data['ptid']);

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));

    //FIXME This should be configuration in the celko property itself
    $data['object']->properties['position']->initialization_celkoparent_id = 'parentpage_id';
    $data['object']->properties['position']->initialization_celkoright_id = 'rightpage_id';
    $data['object']->properties['position']->initialization_celkoleft_id  = 'leftpage_id';
    $xartable =& xarDB::getTables();
    $data['object']->properties['position']->initialization_itemstable = $xartable['publications'];

    $data['properties'] = $data['object']->getProperties();
    $data['items'] = array();

    if (!empty($data['ptid'])) {
        $template = $pubtypeobject->properties['template']->value;
    } else {
        // TODO: allow templates per category ?
        $template = null;
    }

    // Get the settings of the publication type we are using
    $data['settings'] = xarMod::apiFunc('publications', 'user', 'getsettings', array('ptid' => $data['ptid']));
    
    return xarTpl::module('publications', 'admin', 'new', $data, $template);
}
