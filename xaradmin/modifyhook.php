<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * modify an entry for a module item - hook for ('item','modify','GUI')
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo']
 * @param string $args['extrainfo']['keywords'] or 'keywords' from input (optional)
 * @returns string
 * @return hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_admin_modifyhook($args)
{
    extract($args);

    if (empty($extrainfo))
        $extrainfo = array();

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('objectid', 'admin', 'modifyhook', 'keywords');
        throw new BadParameterException($vars, $msg);
    }

    // When called via hooks, the module name may be empty. Get it from current module.
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarMod::getRegId($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module', 'admin', 'modifyhook', 'keywords');
        throw new BadParameterException($vars, $msg);
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    // no permission, no worries, just don't display the form 
    if (!xarSecurityCheck('AddKeywords',0,'Item', "$modid:$itemtype:$itemid")) return '';

    // get settings currently in force for this module/itemtype
    $data = xarMod::apiFunc('keywords', 'hooks', 'getsettings',
        array(
            'module' => $modname,
            'itemtype' => $itemtype,
        ));

    // get the index_id for this module/itemtype/item
    $index_id = xarMod::apiFunc('keywords', 'index', 'getid',
        array(
            'module' => $modname,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
        ));

    // see if keywords were passed to hook call
    if (!empty($extrainfo['keywords'])) {
        $keywords = $extrainfo['keywords'];
    } else {
        // could be an item preview, try fetch from form input
        if (!xarVarFetch('keywords', 'isset',
            $keywords, null, XARVAR_DONT_SET)) return;
    }
    // keywords not supplied
    if (!isset($keywords)) {
        // get the keywords associated with this item
        $keywords = xarMod::apiFunc('keywords', 'words', 'getwords',
            array(
                'index_id' => $index_id,
            ));
    }
    // we may have been given a string list
    if (!empty($keywords) && !is_array($keywords)) {
        $keywords = xarModAPIFunc('keywords','admin','separekeywords',
            array(
                'keywords' => $keywords,
            ));
    }

    // it's ok if there are no keywords
    if (empty($keywords))
        $keywords = array();
    
    // if there are auto tags and they're persistent, add them to keywords
    if (!empty($data['auto_tag_create']) && !empty($data['auto_tag_persist']))
        $keywords = array_merge($keywords, $data['auto_tag_create']);
    

    // Retrieve the list of allowed delimiters
    $delimiters = xarModVars::get('keywords','delimiters');
    $delimiter = !empty($delimiters) ? $delimiters[0] : ',';
    
    if (empty($data['restrict_words'])) {
        // no restrictions, display expects a string
        // Use first delimiter to join words        
        $data['keywords'] = !empty($keywords) ? implode($delimiter, array_unique($keywords)) : '';
    } else {
        // get restricted list based on current settings
        $data['restricted_list'] = xarMod::apiFunc('keywords', 'words', 'getwords',
            array(
                'index_id' => $data['index_id'],
            ));
        // return only keywords that are also in the restricted list
        $data['keywords'] = array_intersect($keywords, $data['restricted_list']);
        // see if managers are allowed to add to restricted list
        if (!empty($data['allow_manager_add'])) {
            // see if current user is a manager
            $data['is_manager'] = xarSecurityCheck('ManageKeywords',0,'Item', "$modid:$itemtype:$itemid");
            if (!empty($data['is_manager'])) {
                // see if keywords were passed to hook call
                if (!empty($extrainfo['restricted_extra'])) {
                    $toadd = $extrainfo['restricted_extra'];
                } else {
                    // could be an item preview, try fetch from form input
                    if (!xarVarFetch('restricted_extra', 'isset',
                        $toadd, array(), XARVAR_NOT_REQUIRED)) return;
                }
                // we may have been given a string list
                if (!empty($toadd) && !is_array($toadd)) {
                    $toadd = xarModAPIFunc('keywords','admin','separekeywords',
                        array(
                            'keywords' => $toadd,
                        ));
                }
                // display expects a string 
                $data['restricted_extra'] = !empty($toadd) ? implode($delimiter, array_unique($toadd)) : '';
            }
        }
        
    }
    $data['delimiters'] = $delimiters;

    return xarTpl::module('keywords', 'admin', 'modifyhook', $data);

}
?>