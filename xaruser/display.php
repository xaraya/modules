<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * display sharecontent for a specific item, and request sharecontent
 * @param $args['objectid'] ID of the item this sharecontent is for
 * @param $args['extrainfo'] array of item information: module, returnurl, itemtype
 * @return output with sharecontent information
 */
function sharecontent_user_display($args)
{
    extract($args);

    $data = array();
    $data['objectid'] = $objectid;

    $itemtype = 0;
    if (isset($extrainfo) && is_array($extrainfo)) {
        if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        }
        if (isset($extrainfo['returnurl']) && is_string($extrainfo['returnurl'])) {
            $data['returnurl'] = $extrainfo['returnurl'];
			unset($extrainfo['returnurl']);
        }
    } else {
        $data['returnurl'] = $extrainfo;
    }

    if (empty($modname)) {
        $modname = xarModGetName();
    }

    $extrainfo['modid'] = xarModGetIDFromName($modname);
    // get only enabled sites
    $args['active']=1;

    // Run API function
    if ($websites = xarModAPIFunc('sharecontent', 'user', 'get',$args)) {
        if (isset($websites)) {
            // Set the cached variable if requested
            if (xarVarIsCached('Hooks.sharecontent','save') &&
                xarVarGetCached('Hooks.sharecontent','save') == true) {
            	xarVarSetCached('Hooks.sharecontent','value',$websites);
            } 
            
            foreach($websites as $key=>$website) {
                $submiturl = $website['submiturl'];
                //$dataurl = preg_replace('/&amp;/','%2526',$data['returnurl']);
                $dataurl = $data['returnurl'];
                $submiturl = preg_replace('/#URL#/',$dataurl,$submiturl);
                if (isset($extrainfo['title'])) {
      				// needs to do it twice for some sites
				    $submiturl = preg_replace('/#TITLE#/',$extrainfo['title'],$submiturl);
				    $submiturl = preg_replace('/#TITLE#/',$extrainfo['title'],$submiturl);
				}
                $websites[$key]['submiturl']=$submiturl;
            }
        }    
    }    
 
    if (xarModGetVar('sharecontent','enablemail') and 
        xarSecurityCheck('SendSharecontentMail', 0, 'Mail', $modname))
	{
        $data['authid'] = xarSecGenAuthKey('sharecontent');
        $data['usercansend'] = '1';
		$data['extrainfo'] = serialize($extrainfo);
    } else {
    	$data['usercansend'] = '0';
    }

    if (xarSecurityCheck('ReadSharecontentWeb', 0, 'All', $modname)) {
        $data['websites'] = $websites;
    }  else {
        $data['websites'] = array();
    }
	
    return $data;
}

?>
