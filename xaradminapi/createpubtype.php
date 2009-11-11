<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Create a new publication type
 *
 * @param $args['name'] name of the publication type
 * @param $args['descr'] description of the publication type
 * @param $args['config'] configuration of the publication type
 * @param $args['settings'] optional settings for the publication type
 * @param $args['cids'] optional base categories for the publication type
 * @return int publication type ID on success, false on failure
 */
function articles_adminapi_createpubtype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
    $invalid = array();
    if (!isset($name) || !is_string($name) || empty($name)) {
        $invalid[] = 'name';
    }
    if (!isset($config) || !is_array($config) || count($config) == 0) {
        $invalid[] = 'configuration';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'createpubtype','Articles');
        throw new BadParameterException(null,$msg);
    }

    if (empty($descr)) {
        $descr = $name;
    }

    // Publication type names *must* be lower-case for now
    $name = strtolower($name);

    // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminArticles')) return;

    if (!xarModAPILoad('articles', 'user')) return;

    // Make sure we have all the configuration fields we need
    $pubfields = xarMod::apiFunc('articles','user','getpubfields');
    foreach ($pubfields as $field => $value) {
        if (!isset($config[$field])) {
            $config[$field] = '';
        }
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $pubtypestable = $xartable['publication_types'];

    // Get next ID in table
    $nextId = $dbconn->GenId($pubtypestable);

    // Insert the publication type
    $query = "INSERT INTO $pubtypestable (xar_pubtypeid, xar_pubtypename,
            xar_pubtypedescr, xar_pubtypeconfig)
            VALUES (?,?,?,?)";
    $bindvars = array($nextId, $name, $descr, serialize($config));
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get ptid to return
    $ptid = $dbconn->PO_Insert_ID($pubtypestable, 'xar_pubtypeid');

    // Don't call creation hooks here...
    //xarModCallHooks('item', 'create', $ptid, 'ptid');

    if (empty($ptid)) {
        return $ptid;
    }

    // Initialize settings for this publication type
    if (empty($settings)) {
        if (empty($config['status']) || empty($config['status']['label'])) {
            $status = 2;
        } else {
            $status = 0;
        }
        $settings = array('number_of_columns'    => 0,
                          'itemsperpage'         => 20,
                          'defaultview'          => 1,
                          'showcategories'       => 1,
                          'showcatcount'         => 0,
                          'showprevnext'         => 0,
                          'showcomments'         => 1,
                          'showhitcounts'        => 1,
                          'showratings'          => 0,
                          'showarchives'         => 1,
                          'showmap'              => 1,
                          'showpublinks'         => 0,
                          'showpubcount'         => 0,
                          'dotransform'          => 0,
                          'titletransform'       => 0,
                          'prevnextart'          => 0,
                          'usealias'             => 0,
                          'page_template'        => '',
                          'usetitleforurl'       => 0,
                          'defaultstatus'        => $status,
                          'defaultsort'          => 'date');
    }
    if (is_array($settings)) {
        // check the base categories by name if specified
        if (empty($cids) && !empty($settings['categories'])) {
            if (is_array($settings['categories'])) {
                $catnames = $settings['categories'];
            } else {
                $catnames = explode(';',$settings['categories']);
            }
            $cids = array();
            foreach ($catnames as $catname) {
                // try to find a category that matches this name
                $categories = xarMod::apiFunc('categories','user','getcatbyname',
                                              array('name' => $catname));
                if (empty($categories)) {
                    $cid = xarMod::apiFunc('categories', 'admin', 'create',
                                           array('name'        => $catname,
                                                 'description' => $catname,
                                                 'parent_id'   => 0));
                    // add as base category if necessary
                    if (!empty($cid) && !in_array($cid, $cids)) {
                        $cids[] = $cid;
                    }
                } else {
                    // cfr. format returned by getcat
                    $category = array_pop($categories);
                    // add as base category if necessary
                    if (!empty($category['cid']) && !in_array($category['cid'], $cids)) {
                        $cids[] = $category['cid'];
                    }
                }
            }
        }
        unset($settings['categories']);
        // check the default view
        if (!empty($settings['defaultview']) && !is_numeric($settings['defaultview'])) {
            // default view is a category name
            $catname = $settings['defaultview'];
            // set default view to latest items
            $settings['defaultview'] = 1;
            // try to find a category that matches this name
            $categories = xarMod::apiFunc('categories','user','getcatbyname',
                                          array('name' => $catname));
            if (empty($categories)) {
                $cid = xarMod::apiFunc('categories', 'admin', 'create',
                                       array('name'        => $catname,
                                             'description' => $catname,
                                             'parent_id'   => 0));
                // set as base category if necessary
                if (empty($cids) && !empty($cid)) {
                    $cids = array($cid);
                }
            } else {
                // cfr. format returned by getcat
                $category = array_pop($categories);
                if (!empty($category['cid'])) {
                    // set default view to browse in this category
                    $settings['defaultview'] = 'c' . $category['cid'];
                    // set as base category if necessary
                    if (empty($cids)) {
                        $cids = array($category['cid']);
                    }
                }
            }
        }
        $settings = serialize($settings);
    }
    xarModVars::set('articles', 'settings.'.$ptid, $settings);

    // Create corresponding dd object as articles_[name] for now
    sys::import('modules.dynamicdata.class.objects.master');
    DataObjectMaster::createObject(array('name'     => 'articles_' . $name,
                                         'label'    => $descr,
                                         'moduleid' => 151,
                                         'itemtype' => $ptid,
                                         //'class'    => 'ArticleObject', TODO
                                         //'filepath' => 'modules/articles/class/article.php', TODO
                                         'urlparam' => 'itemid',
                                         'config'   => $settings));

    // Set base categories for this publication type
    if (empty($cids)) {
        $cids = null;
    }
    xarMod::apiFunc('articles','admin','setrootcats',
                  array('ptid' => $ptid,
                        'cids' => $cids));

    // Return the publication type id
    return $ptid;
}

?>
