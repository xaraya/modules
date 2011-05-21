<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_templates_type($args)
{
    if (!xarSecurityCheck('AdminPublications')) return;

    extract($args);

    if (!xarVarFetch('confirm',        'int',    $confirm,       0,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('ptid',        'id',    $data['ptid'],       xarModVars::get('publications', 'defaultpubtype'),  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('file',        'str',   $data['file'],       'summary',  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('source_data',        'str',   $data['source_data'],       '',  XARVAR_NOT_REQUIRED)) {return;}

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $pubtype = explode('_',$pubtypeobject->properties['name']->value);
    $pubtype = isset($pubtype[1]) ? $pubtype[1] : $pubtype[0];
    
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));

    $basepath = sys::code() . "modules/publications/xartemplates/objects/" . $pubtype;
    $source = $basepath . "/" . $data['file'] . ".xt";

    if ($confirm && !empty($data['source_data'])) {
        xarMod::apiFunc('publications', 'admin', 'write_file', array('file' => $source, 'data' => $data['source_data']));
    }
    
    $data['source_data'] = trim(xarMod::apiFunc('publications', 'admin', 'read_file', array('file' => $source)));

    // Initialize the template
    if (empty($data['source_data'])) {
        $source_dist = $basepath . "/" . $data['file'] . "_dist.xt";
        $data['source_data'] = xarMod::apiFunc('publications', 'admin', 'read_file', array('file' => $source_dist));
        xarMod::apiFunc('publications', 'admin', 'write_file', array('file' => $source, 'data' => $data['source_data']));
    }
    
    $data['files'] = array(
        array('id' => 'summary', 'name' => 'summary display'),
        array('id' => 'detail',  'name' => 'detail display'),
        array('id' => 'input',   'name' => 'input form'),
    );
    return $data;
}
?>