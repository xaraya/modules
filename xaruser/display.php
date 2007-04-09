<?php
/**
 * Webshare Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage webshare Module
 * @link http://xaraya.com/index.php/release/883.html
 * @author Andrea Moro
 */
/**
 * display webshare for a specific item, and request webshare
 * @param $args['objectid'] ID of the item this webshare is for
 * @param $args['extrainfo'] array of item information: module, returnurl, itemtype
 * @return output with webshare information
 */
function webshare_user_display($args)
{
    extract($args);

    $data = array();
    $data['objectid'] = $objectid;

    $itemtype = 0;
    if (isset($extrainfo) && is_array($extrainfo)) {
        if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        }
        if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
        if (isset($extrainfo['returnurl']) && is_string($extrainfo['returnurl'])) {
            $data['returnurl'] = $extrainfo['returnurl'];
        }
        if (isset($extrainfo['title']) && is_string($extrainfo['title'])) {
            $data['title'] = $extrainfo['title'];
        }
    } else {
        $data['returnurl'] = $extrainfo;
    }

    if (empty($modname)) {
        $modname = xarModGetName();
    }

    $args['active']=1;
    // Run API function
    if ($websites = xarModAPIFunc('webshare', 'user', 'get',$args)) {

    if (isset($websites)) {
	// Set the cached variable if requested
	    if (xarVarIsCached('Hooks.webshare','save') &&
	     	xarVarGetCached('Hooks.webshare','save') == true) {
			xarVarSetCached('Hooks.webshare','value',$websites);
        } 
    
        foreach($websites as $key=>$website) {
    		$submiturl = $website['submiturl'];
    		$submiturl = preg_replace('/#URL#/',$data['returnurl'],$submiturl);
    		$submiturl = preg_replace('/#TITLE#/',$data['title'],$submiturl);
    		$websites[$key]['submiturl']=$submiturl;
    	}
    }    
    }    

    // set an authid, but only if the current user can rate the item
    if (xarSecurityCheck('ReadWebshareWeb', 0, 'All', "All")) {
	    $data['websites'] = $websites;
    }  else {
	    $data['websites'] = array();
    }

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
