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
    if (!xarSecurityCheck('ModeratePublications')) return;

    extract($args);

    // Get parameters
    if (!xarVarFetch('ptid',        'id',    $data['ptid'],       xarModVars::get('publications', 'defaultpubtype'),  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('catid',       'str',   $catid,      NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype',    'id',    $itemtype,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    $data['properties'] = $data['object']->getProperties();
    

    if (!empty($data['ptid'])) {
        $template = $pubtypeobject->properties['template']->value;
    } else {
// TODO: allow templates per category ?
       $template = null;
    }

    // Get the settings of the publication type we are using
    $data['settings'] = xarModAPIFunc('publications','user','getsettings',array('ptid' => $data['ptid']));
    
    return xarTplModule('publications', 'user', 'new', $data, $template);
}

?>