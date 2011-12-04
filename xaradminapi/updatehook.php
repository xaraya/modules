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
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['keywords'] from arguments, or 'keywords' from input
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo'] extra information
 * @return mixed true on success, false on failure. string keywords list
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_updatehook($args)
{
    extract($args);

    if (empty($extrainfo))
        $extrainfo = array();

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('objectid', 'admin', 'updatehook', 'keywords');
        throw new BadParameterException($vars, $msg);
    }

    // We can exit immediately if the status flag is set because we are just updating
    // the status in the articles or other content module that works on that principle
    // Bug 1960 and 3161
    if (xarVarIsCached('Hooks.all','noupdate') || !empty($extrainfo['statusflag'])){
        return $extrainfo;
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
        $vars = array('module', 'admin', 'updatehook', 'keywords');
        throw new BadParameterException($vars, $msg);
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    // @todo: replace this with access prop
    if (!xarSecurityCheck('AddKeywords',0,'Item', "$modid:$itemtype:$itemid"))
        return $extrainfo;

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

    // get the current keywords associated with this item
    $oldwords = xarMod::apiFunc('keywords', 'words', 'getwords',
        array(
            'index_id' => $index_id,
        ));

    if (!empty($settings['restrict_words'])) {
        $restricted_list = xarMod::apiFunc('keywords', 'words', 'getwords',
            array(
                'index_id' => $settings['index_id'],
            ));
        // store only keywords that are also in the restricted list
        $keywords = array_intersect($keywords, $restricted_list);
    }
    $toadd = array_filter(array_unique(array_diff($keywords, $oldwords)));
    $toremove = array_filter(array_unique(array_diff($oldwords, $keywords)));

    if (!empty($toadd)) {
        if (!xarMod::apiFunc('keywords', 'words', 'createitems',
            array(
                'index_id' => $index_id,
                'keyword' => $toadd,
            ))) return;
    }
    if (!empty($toremove)) {
        if (!xarMod::apiFunc('keywords', 'words', 'deleteitems',
            array(
                'index_id' => $index_id,
                'keyword' => $toremove,
            ))) return;
    }

    // Retrieve the list of allowed delimiters
    $delimiters = xarModVars::get('keywords','delimiters');
    $delimiter = !empty($delimiters) ? $delimiters[0] : ',';
    $extrainfo['keywords'] = implode($delimiter, $keywords);

    return $extrainfo;
}
?>