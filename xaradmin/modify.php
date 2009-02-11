<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * modify publication
 * @param int id The ID of the publication
 * @param string return_url
 * @param int preview
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_modify($args)
{
    extract($args);

    // Get parameters
    if (!xarVarFetch('itemid','isset', $id, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('return_url', 'str:1', $data['return_url'], NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('name', 'str:1', $name, NULL, XARVAR_NOT_REQUIRED)) {return;}

    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $id));
    $data['properties'] = $data['object']->getProperties();

    $data['ptid'] = $data['properties']['itemtype']->value;
    
    if (!empty($ptid)) {
        $template = $pubtypes[$ptid]['name'];
    } else {
// TODO: allow templates per category ?
       $template = null;
    }

    return xarTplModule('publications', 'admin', 'modify', $data, $template);



     $ptid = $publication['pubtype_id'];
    if (!isset($ptid)) {
       $ptid = '';
    }
    $data = array();
    $data['ptid'] = $ptid;
    $data['id'] = $id;

    $pubtypes = xarModAPIFunc('publications','user','getpubtypes');

    // Security check
    $input = array();
    $input['publication'] = $publication;
    $input['mask'] = 'EditPublications';
    if (!xarModAPIFunc('publications','user','checksecurity',$input)) {
        $msg = xarML('You have no permission to modify #(1) item #(2)',
                     $pubtypes[$ptid]['descr'], xarVarPrepForDisplay($id));
        throw new ForbiddenOperationException(null, $msg);
    }
    unset($input);

}

?>