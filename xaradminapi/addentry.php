<?php
/**
 * Add a term to the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_adminapi_addentry($args)
{
    if (!xarSecurityCheck('AddEncyclopedia',1,'Entry')) {return;}
    extract($args);

    if(!xarVarFetch('id', 'int', $vars['id'], 0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('vid', 'int', $vars['vid'], 0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('term', 'str', $vars['term'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('pronunciation', 'str', $vars['pronunciation'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('related', 'str', $vars['related'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('links', 'str', $vars['links'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('definition', 'str', $vars['definition'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('author', 'str', $vars['author'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('active', 'int', $vars['active'], 0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('date', 'str', $vars['date'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('file', 'str', $file, '',  XARVAR_NOT_REQUIRED)) {return;}

    $objectid = xarModGetVar('encyclopedia','encyclopediaid');
    $object = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                            array('objectid' => $objectid));

    $itemid = xarModAPIFunc('dynamicdata', 'admin', 'create',
                            array('modid'    => $object['moduleid'],
                                  'itemtype' => $object['itemtype'],
                                  'itemid' => 0,
                                  'values'   => $vars));

    if (($file != "none") && ($file != "") && ($file != "index")) {
        $imgsize = getimagesize($file);
        if (($imgsize[0] > xarModGetVar('Encyclopedia', 'imagewidth')) || ($imgsize[1] > xarModGetVar('Encyclopedia', 'imageheight'))) {
            unlink($file);
            return false;
        } else if (($imgsize[2] != 1) && ($imgsize[2] != 2)) {
            unlink($file);
            return false;
        } else {
            if ($imgsize[2] == 1) $ext = ".gif";
            if ($imgsize[2] == 2) $ext = ".jpg";
            // Store image on filesystem
            $outfile = "modules/encyclopedia/pictures/" . $vars['id'] . $ext;

            // Move image data into file
            $infh = fopen($file, 'r');
            $outfh = fopen($outfile, 'w');
            while ($imagebit = fread($infh, 16384)) {
                fwrite($outfh, $imagebit);
            }
            fclose($infh);
            fclose($outfh);

            $propid = xarModAPIFunc('dynamicdata', 'admin', 'update',
                                    array('modid'    => $object['moduleid'],
                                          'itemtype' => $object['itemtype'],
                                          'itemid'   => $itemid,
                                          'values'   => array('file' => $file)));
        }
    }
    xarModCallHooks('item', 'addentry', $itemid, 'id');
    return $itemid;
}

?>