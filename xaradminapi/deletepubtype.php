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
 * Delete a publication type
 *
 * @param $args['ptid'] ID of the publication type
 * @return bool true on success, false on failure
 */
function articles_adminapi_deletepubtype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    if (!isset($ptid) || !is_numeric($ptid) || $ptid < 1) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type ID', 'admin', 'deletepubtype',
                    'Articles');
        throw new BadParameterException(null,$msg);
    }

    // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminArticles',1,'Article',"$ptid:All:All:All")) return;

    // Load user API to obtain item information function
    if (!xarModAPILoad('articles', 'user')) return;

    // Get current publication types
    $pubtypes = xarMod::apiFunc('articles','user','getpubtypes');
    if (!isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type ID', 'admin', 'deletepubtype',
                    'Articles');
        throw new BadParameterException(null,$msg);
    }

    // Clear base categories for this publication type
    xarMod::apiFunc('articles','admin','setrootcats',
                  array('ptid' => $ptid,
                        'cids' => null));

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $pubtypestable = $xartable['publication_types'];

    // Delete the publication type
    $query = "DELETE FROM $pubtypestable
            WHERE xar_pubtypeid = ?";
    $result =& $dbconn->Execute($query,array($ptid));
    if (!$result) return;

    $articlestable = $xartable['articles'];

    // Delete all articles for this publication type
    $query = "DELETE FROM $articlestable
            WHERE xar_pubtypeid = ?";
    $result =& $dbconn->Execute($query,array($ptid));
    if (!$result) return;

// TODO: call some kind of itemtype remove hooks here, once we have those
    //xarModCallHooks('module', 'remove', 'articles',
    //                array('module' => 'articles',
    //                      'itemtype' => $ptid));


    // Delete settings for this publication type
    xarModVars::delete('articles', 'settings.'.$ptid);

    // Delete this publication type as module alias for articles
    xarModDelAlias($pubtypes[$ptid]['name'],'articles');

    // Remove this publication type as default if necessary
    $default = xarModVars::get('articles','defaultpubtype');
    if ($ptid == $default) {
        xarModVars::set('articles','defaultpubtype','');
    }

    // Delete corresponding dd object if any
    sys::import('modules.dynamicdata.class.objects.master');
    $objects = DataObjectMaster::getObjects(array('moduleid' => 151));
    foreach ($objects as $objectinfo) {
        // find dd object with corresponding itemtype
        if ($objectinfo['moduleid'] == 151 && $objectinfo['itemtype'] == $ptid) {
            DataObjectMaster::deleteObject(array('objectid' => $objectinfo['objectid']));
        }
    }

    return true;
}

?>
