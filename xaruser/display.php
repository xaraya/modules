<?php
/**
 * Display an Item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * display an item
 
 * @return array $data
 */
function content_user_display($args)
{ 
	extract($args);
	$xarmoduletag = !empty($args) ? true : false;

    if(!xarVarFetch('itemid',   'id', $itemid,   NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('page_template', 'str', $page_template, NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!$itemid) {
		$defaultcheck = true;
		$itemid = xarModVars::get('content','default_itemid');
		if (!is_numeric($itemid)) {
			$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
						'item id', 'user', 'display', 'content');
			throw new Exception($msg);
		}
    }

	sys::import('modules.dynamicdata.class.objects.master');

	// Get the object name
	$contentobject = DataObjectMaster::getObject(array('name' => 'content'));
	$contentobject->getItem(array('itemid' => $itemid));
	$ctype = $contentobject->properties['content_type']->getValue();
 
	$instance = $itemid.':'.$ctype.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('ReadContent',1,'Item',$instance)) return;

	if (xarSecurityCheck('EditContent',0,'Item',$instance)) {
		$data['editurl'] = xarModURL('content','admin','modify',array('itemid' => $itemid));
	}

	$data['ctype'] = $ctype;

	// Get the item's field values
    $object = DataObjectMaster::getObject(array('name' => $ctype));
	$check = $object->getItem(array('itemid' => $itemid));

	if (!($check)) { 
		if ($xarmoduletag) return;  //don't show a 404 if we're using a xar:module tag
		$msg = 'You\'re seeing this message because the itemid ' . $itemid . ' does not exist in the content module.';
		if ($itemid == xarModVars::get('content','default_itemid')) {
			$msg .= ' Please check the Default Itemid setting for the content module to be sure it is set to an itemid that exists.';
		}  
		return xarTplModule('base','message','notfound',array('msg' => $msg));
	}

    $values = $object->getFieldValues();

	if (isset($values['publication_status'])) {
		if($values['publication_status'] < 2 && !xarSecurityCheck('EditContent',0,'Item',$instance)) {
			return;
		}
	}

	if (isset($values['publication_date']) && $values['publication_date'] > time()) return;

	// publication_date should never be empty, but expiration_date may be empty
	if (isset($values['expiration_date']) && $values['expiration_date'] < time()) return;

	if (isset($values['display_template']) && !empty($values['display_template'])) { // if a display_template is set, first look for that
		$dtemplate = $values['display_template']; 
		if ($dtemplate != '-inherit-') {
			if ($dtemplate == 'user-display') {
				$template = '';
			} else {
				$itemtemplate = xarTpl__getSourceFileName('content','user-display',$dtemplate);
				if (strstr($itemtemplate, $dtemplate)) {
					$template = $dtemplate;
				}  
			}
		}
	}

	$data['itemid'] = $itemid;
			
	foreach ($values as $name => $value) {
		$data[$name] = $value;
	}

	if (!isset($template)) { // either no display template is set or we couldn't find it...
	
		$template = 'itemid' . $itemid;
		$itemtemplate = xarTpl__getSourceFileName('content','user-display',$template);
		if (!strstr($itemtemplate, $template)) {
			$template = $data['ctype'];
		}  

		// First look for something like user-display-itemid<123>.xt  
		// then look for something like user-display-<content_type>.xt
		// then fall back on user-display.xt
		$template = 'itemid' . $itemid;
		$itemtemplate = xarTpl__getSourceFileName('content','user-display',$template);
		if (!strstr($itemtemplate, $template)) {
			$template = $data['ctype'];
		} 
	}

	// first see if the $pagetpl is set explicitly for this call
	if (isset($page_template) && !empty($page_template)) {   
		xarTplSetPageTemplateName($page_template);
	} else { 
		// see if this content type has a page template configured
		$config = $object->configuration;
		if (!empty($config['page_template'])) {  
			xarTplSetPageTemplateName($config['page_template']);
		} else { 
			$pagetpl = xarModVars::get('content','default_display_page_tpl');  
			if (empty($pagetpl)) $pagetpl = 'default';
			xarTplSetPageTemplateName($pagetpl);
		}
	}

	$item['returnurl'] = xarServer::getCurrentURL();
    $data['hooks'] = xarModCallHooks('item', 'display', $itemid, $item);

    return xarTplModule('content','user','display', $data, $template);

}

?>