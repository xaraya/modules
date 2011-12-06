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
 * create an entry for a module item - hook for ('item','create','GUI')
 * Optional $extrainfo['keywords'] from arguments, or 'keywords' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array Extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_createhook($args)
{
    extract($args);

    if (empty($extrainfo))
        $extrainfo = array();

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('objectid', 'admin', 'createhook', 'keywords');
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
        $vars = array('module', 'admin', 'newhook', 'keywords');
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

    // get settings currently in force for this module/itemtype
    $settings = xarMod::apiFunc('keywords', 'hooks', 'getsettings',
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
        // otherwise, try fetch from form input
        if (!xarVarFetch('keywords', 'isset',
            $keywords, null, XARVAR_DONT_SET)) return;
        // keywords from form input, check current user has permission to add keywords here 
        if (!empty($keywords) && !xarSecurityCheck('AddKeywords',0,'Item', "$modid:$itemtype:All"))
            return $extrainfo;  // no permission, no worries
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

    // see if we are auto tagging new items, and add words to list 
    if (!empty($settings['auto_tag_create'])) 
        $keywords = array_merge($keywords, $settings['auto_tag_create']);

    if (!empty($settings['restrict_words'])) {
        $restricted_list = xarMod::apiFunc('keywords', 'words', 'getwords',
            array(
                'index_id' => $settings['index_id'],
            ));
        // store only keywords that are also in the restricted list
        $keywords = array_intersect($keywords, $restricted_list);
        // see if managers are allowed to add to restricted list
        if (!empty($settings['allow_manager_add'])) {
            // see if current user is a manager
            $data['is_manager'] = xarSecurityCheck('ManageKeywords',0,'Item', "$modid:$itemtype:All");
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
                if (!empty($toadd)) {
                    // add words to restricted list
                    if (!xarMod::apiFunc('keywords', 'words', 'createitems',
                        array(
                            'index_id' => $settings['index_id'],
                            'keyword' => array_unique(array_diff($toadd, $keywords)),
                        ))) return;
                    // merge words with existing keywords 
                    $keywords = array_merge($keywords, $toadd);
                }
            }
        }
    }
    $keywords = array_filter(array_unique($keywords));

    // keywords may be empty after restrictions and filters are applied
    if (empty($keywords))
        return $extrainfo; //$keywords = array();

    // have keywords, create associations
    if (!xarMod::apiFunc('keywords', 'words', 'createitems',
        array(
            'index_id' => $index_id,
            'keyword' => $keywords,
        ))) return;

    // Retrieve the list of allowed delimiters
    $delimiters = xarModVars::get('keywords','delimiters');
    $delimiter = !empty($delimiters) ? $delimiters[0] : ',';
    $extrainfo['keywords'] = implode($delimiter, $keywords);

    return $extrainfo;
}
?>