<?php
/**
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Import a content type
 */
function content_util_import($args) {

    if(!xarSecurityCheck('AdminContentTypes')) return;

	xarTplSetPageTemplateName('admin');

    if(!xarVarFetch('xml',        'isset', $xml,         NULL,  XARVAR_DONT_SET)) {return;} 

    extract($args);

    $data['warning'] = '';

    $data['authid'] = xarSecGenAuthKey();

    if (!empty($xml)) {
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        
      
		$objectid = xarMod::apiFunc('dynamicdata','util','import',
								  array('xml' => $xml,
										'keepitemid' => 0,
										'overwrite' =>  0,
										'prefix' => '',
										));
      
        if (empty($objectid)) return;

		sys::import('modules.dynamicdata.class.objects.master');

		$object = DataObjectMaster::getObject(array('name' => 'objects'));
 
		// proceed
		$object->getItem(array('itemid' => $objectid));
		$name = $object->properties['name']->value;
		$label = $object->properties['label']->value;

		$ctobject = DataObjectMaster::getObject(array('name' => 'content_types'));
		$ctobject->properties['label']->setValue($label);
		$ctobject->properties['content_type']->setValue($name);
		$ctobject->properties['model']->setValue('imported');
		$itemid = $ctobject->createItem(array('itemid' => $objectid));

        xarResponse::redirect(xarModURL('content', 'admin', 'modifycontenttype', array('itemid' => $objectid, 'ctype' => $name)));
        return true;
    }

    return $data;

}

?>