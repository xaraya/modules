<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Re-scan all file associations (possibly for a specific module, itemtype and itemid)
 *
 *  @author  mikespub
 *  @access  public
 *
 *  @param   integer modid     The id of module we are going to rescan
 *  @param   integer itemtype  The item type within the defined module
 *  @param   integer itemid    The id of the item types item
 *  @param   integer fileId    The id of the file we are going to rescan
 *  @return mixed TRUE on success, void with exception on error
 */

function uploads_adminapi_rescan_associations( $args )
{

// FIXME: don't use this as such in the uploads_guimods version, because you'd
//        loose information about the categories and direct file associations

    // 1. delete any existing associations for these arguments
    if (!xarModAPIFunc('uploads','user','db_delete_association', $args)) return;

    extract($args);

    // 2. get the upload-related property types
    $proptypes = xarModAPIFunc('dynamicdata','user','getproptypes');
    $proptypelist = array();
    foreach ($proptypes as $typeid => $proptype) {
        if ($proptype['name'] == 'uploads' || $proptype['name'] == 'fileupload' || $proptype['name'] == 'textupload') {
            $proptypelist[$typeid] = $proptype['name'];
        }
    }

    // 3. get the list of dynamic objects we're interesting in
    if (!empty($modid)) {
        $objectinfolist = array();
        $objectinfolist[] = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                   array('modid' => $modid,
                                         'itemtype' => isset($itemtype) ? $itemtype : null));
    } else {
        $objectinfolist = xarModAPIFunc('dynamicdata','user','getobjects');
    }

    // 4. for each dynamic object
    $modnames = array();
    foreach ($objectinfolist as $objectinfo) {
        if (empty($objectinfo['objectid'])) continue;

        // 5. get the module name for later
        $modid = $objectinfo['moduleid'];
        $itemtype = $objectinfo['itemtype'];
        if (!isset($modnames[$modid])) {
            $modinfo = xarModGetInfo($modid);
            if (empty($modinfo)) return;
            $modnames[$modid] = $modinfo['name'];
        }

        // 6. get a dynamic object list
        $object = xarModAPIFunc('dynamicdata','user','getobjectlist', $objectinfo);

        // 7. build the list of properties we're interested in
        $proplist = array();
        $wherelist = array();
        foreach (array_keys($object->properties) as $propname) {
            $proptype = $object->properties[$propname]->type;
            if (!isset($proptypelist[$proptype])) {
                continue;
            }
            // see if uploads is hooked where necessary
            if (($proptypelist[$proptype] == 'fileupload' || $proptypelist[$proptype] == 'textupload') &&
                !xarModIsHooked('uploads', $modnames[$modid], $itemtype)) {
                // skip this property
                continue;
            }
            // add this property to the list
            $proplist[$propname] = $proptypelist[$proptype];
            // we're only interested in items with non-empty values
            $wherelist[] = "$propname ne ''";
        }
        if (empty($proplist)) continue;

        // 8. get the items and properties we're interested in
        $object->getItems(array('itemids'   => !empty($args['itemid']) ? array($args['itemid']) : null,
                                'fieldlist' => array_keys($proplist),
                                'where'     => join(' and ', $wherelist)));
        if (empty($object->items)) continue;

        // 9. analyze the values for file associations
        foreach ($object->items as $itemid => $fields) {
            foreach ($fields as $name => $value) {
                if ($proplist[$name] == 'textupload') {
                    // scan for #ulid:NN# and #file*:NN# in the text - cfr. uploads transformhook
                    if (!preg_match_all('/#(ul|file)\w*:(\d+)#/',$value,$matches)) continue;
                    foreach ($matches[2] as $file) {
                        // Note: we may have more than one association between item and file here
                        xarModAPIFunc('uploads','user','db_add_association',
                                      array('modid'    => $modid,
                                            'itemtype' => $itemtype,
                                            'itemid'   => $itemid,
                                            'fileId'   => $file));
                    }
                } else {
                    // get the file id's directly from the value
                    $files = explode(';',$value);
                    foreach ($files as $file) {
                        if (empty($file) || !is_numeric($file)) continue;
                        // Note: we may have more than one association between item and file here
                        xarModAPIFunc('uploads','user','db_add_association',
                                      array('modid'    => $modid,
                                            'itemtype' => $itemtype,
                                            'itemid'   => $itemid,
                                            'fileId'   => $file));
                    }
                }
            }
        }
    }

    // let's try some articles fields too
    if (!xarModIsAvailable('articles')) return TRUE;
    $artmodid = xarModGetIDFromName('articles');
    if (!empty($args['modid']) && $args['modid'] != $artmodid) return TRUE;

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
    foreach ($pubtypes as $pubtypeid => $pubtypeinfo) {
        if (!empty($args['itemtype']) && $args['itemtype'] != $pubtypeid) continue;
        if (!xarModIsHooked('uploads','articles',$pubtypeid)) continue;
        $fieldlist = array();
        foreach ($pubtypeinfo['config'] as $fieldname => $fieldinfo) {
            if ($fieldinfo['format'] == 'fileupload' || $fieldinfo['format'] == 'textupload') {
                $fieldlist[] = $fieldname;
            }
        }
        if (empty($fieldlist)) continue;
        $articles = xarModAPIFunc('articles','user','getall',
                                  array('aids'   => !empty($args['itemid']) ? array($args['itemid']) : null,
                                        'ptid'   => $pubtypeid,
                                        'fields' => $fieldlist));
        if (empty($articles)) continue;
        foreach ($articles as $article) {
            foreach ($fieldlist as $field) {
                if (empty($article[$field])) continue;
                if ($pubtypeinfo['config'][$field]['format'] == 'textupload') {
                    // scan for #ulid:NN# and #file*:NN# in the text - cfr. uploads transformhook
                    if (!preg_match_all('/#(ul|file)\w*:(\d+)#/',$article[$field],$matches)) continue;
                    foreach ($matches[2] as $file) {
                        // Note: we may have more than one association between item and file here
                        xarModAPIFunc('uploads','user','db_add_association',
                                      array('modid'    => $artmodid,
                                            'itemtype' => $pubtypeid,
                                            'itemid'   => $article['aid'],
                                            'fileId'   => $file));
                    }
                } else {
                    // get the file id's directly from the value
                    $files = explode(';',$article[$field]);
                    foreach ($files as $file) {
                        if (empty($file) || !is_numeric($file)) continue;
                        // Note: we may have more than one association between item and file here
                        xarModAPIFunc('uploads','user','db_add_association',
                                      array('modid'    => $artmodid,
                                            'itemtype' => $pubtypeid,
                                            'itemid'   => $article['aid'],
                                            'fileId'   => $file));
                    }
                }
            }
       }
   }

    return TRUE;
}

?>
