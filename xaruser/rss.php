<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Configures a comments RSS output
 *
 * @author John Cox
 * @access public
 * @returns array
 */
function comments_user_rss($args)
{
    extract($args);
    if (!xarSecurityCheck('ReadComments',0))
        return;

    // get the list of modules+itemtypes that comments is hooked to
    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'comments'));

    // initialize list of module and pubtype names
    $items   = array();
    $modlist = array();
    $modname = array();
    $modview = array();
    $modlist['all'] = xarML('All');
    // make sure we only retrieve comments from hooked modules
    $todolist = array();
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $module => $value) {
            $modid = xarModGetIDFromName($module);
            if (!isset($modname[$modid])) $modname[$modid] = array();
            if (!isset($modview[$modid])) $modview[$modid] = array();
            $modname[$modid][0] = ucwords($module);
            $modview[$modid][0] = xarModURL($module,'user','view');
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($module,'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            if (!empty($mytypes) && count($mytypes) > 0) {
                 foreach (array_keys($mytypes) as $itemtype) {
                     $modname[$modid][$itemtype] = $mytypes[$itemtype]['label'];
                     $modview[$modid][$itemtype] = $mytypes[$itemtype]['url'];
                 }
            }
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                foreach ($value as $itemtype => $val) {
                    $todolist[] = "$module.$itemtype";
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                    }
                    $modlist["$module.$itemtype"] = ucwords($module) . ' - ' . $type;
                }
            } else {
                $todolist[] = $module;
                $modlist[$module] = ucwords($module);
                // allow selecting individual item types here too (if available)
                if (!empty($mytypes) && count($mytypes) > 0) {
                    foreach ($mytypes as $itemtype => $mytype) {
                        if (!isset($mytype['label'])) continue;
                        $modlist["$module.$itemtype"] = ucwords($module) . ' - ' . $mytype['label'];
                    }
                }
            }
        }
    }
    $args['modarray']   = $todolist;
    $args['howmany']    = xarModGetVar('comments', 'rssnumitems');
    $items = xarModAPIFunc('comments','user','get_multipleall', $args);

    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $modinfo = xarModGetInfo($item['modid']);
        $items[$i]['rsstitle']      = htmlspecialchars($item['subject']);
        $linkarray                  = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                                    array('itemtype' => $item['itemtype'],
                                                          'itemids'  => array($item['objectid'])),
                                      // don't throw an exception if this function doesn't exist
                                                     0);
        if (!empty($linkarray)){
            foreach($linkarray as $url){
                $items[$i]['link'] = $url['url'];
            }
        } else {
            // We'll use the comment link instead
            $items[$i]['link'] = xarModUrl('comments', 'user', 'display', array('id' => $item['id']));
        }

        $items[$i]['rsssummary'] = preg_replace('<br />',"\n",$item['text']);
        $items[$i]['rsssummary'] = xarVarPrepForDisplay(strip_tags($item['text']));
    }

    //$output = var_export($items, 1); return "<pre>$output</pre>";
    $data['items'] = $items;
    return $data;
}
?>