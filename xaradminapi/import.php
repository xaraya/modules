<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
function headlines_adminapi_import($args)
{
    extract($args);

    // Check if we have a feed and import pubtype (iid is optional)
    if (empty($hid) || empty($importpubtype)) return 0;

    // The user API function is called
    $links = xarModAPIFunc('headlines', 'user', 'get',
                          array('hid' => $hid));


    // Check and see if a feed has been supplied to us.
    if (empty($links) || empty($links['url'])) return 0;

    $feedfile = $links['url'];

    if (xarModGetVar('headlines', 'magpie')){
        $imports = xarModAPIFunc('magpie',  'user', 'process',
                           array('feedfile' => $feedfile));

    } else {
        $imports = xarModAPIFunc('headlines', 'user', 'process',
                           array('feedfile' => $feedfile));
    }

    if (!empty($imports['warning'])){
        $msg = xarML('There is a problem with this feed : #(1)', $imports['warning']);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

// FIXME: store this elsewhere to avoid bloat on module variables
    $importhistory = xarModGetVar('headlines','importhistory');
    if (empty($importhistory)) {
        $importhistory = '';
    }

    $imported = 0;
    foreach ($imports['feedcontent'] as $import) {
        $sourcelink = array();
        // skip the items we're not interested in (if any)
        if (!empty($iid) && !empty($import['id']) && $iid != $import['id']) continue;
        // skip the items we already imported in the past
        if (!empty($import['id']) && strpos($importhistory,$import['id']) !== FALSE) continue;
        $article             = array();
        $article['title']    = $import['title'];
        $article['summary']  = $import['description'];
        $article['body']     = $imports['chanlink'];
        $sourcelink       = array('link' => $import['link'], 'title' => $imports['chantitle']);
        $article['notes'] = serialize($sourcelink);
        $article['aid']      = 0;
        $article['ptid']     = $importpubtype;
        $article['authorid'] = xarUserGetVar('uid');
        //$article['status'] = 2;
        if (!xarModAPIFunc('articles', 'admin', 'create', $article)) return;
        $imported++;
        if (!empty($import['id'])) {
            $importhistory .= ';' . $import['id'];
        }
    }

    // save the import list, truncating it to max history number
    $historynum = 200;//(int)xarModGetVar('headlines','historynum');

    if (!empty($imported) && !empty($importhistory)) {
        $importlist = split(';',$importhistory);
        $numitems = count($importlist);
        if ($numitems > $historynum && $historynum >= 0) {
            $importlist = array_slice($importlist, $numitems - $historynum);
            $importhistory = join(';',$importlist);
        }
        unset($importlist);
        xarModSetVar('headlines','importhistory',$importhistory);
    }

    return $imported;
}
?>
