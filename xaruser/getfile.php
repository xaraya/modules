<?php
/**
 * Download a file
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Download a file 
 */
function downloads_user_getfile($args) {

        if (!xarVarFetch('itemid', 'id', $itemid, NULL, XARVAR_NOT_REQUIRED)) {return;}

        sys::import('modules.dynamicdata.class.objects.master');

        $object = DataObjectMaster::getObject(array('name' => 'downloads'));
        $object->getItem(array('itemid' => $itemid));
        $filename = $object->properties['filename']->getValue();
        $directory = $object->properties['directory']->getValue();
        $status = $object->properties['status']->getValue();

        if (strstr($filename,'.')) {
            $parts = explode('.',$filename);
            $ext = strtolower(end($parts));
        } else {
            $ext = '';
        }

        $instance = $itemid.':'.$ext.':'.xarUserGetVar('id');
        if (!xarSecurityCheck('ReadDownloads',1,'Record',$instance)) return;

        $basepath = xarMod::apiFunc('downloads','admin','getbasepath');
        xarMod::apiFunc('downloads','user','getfile',array(
            'basepath' => $basepath,
            'directory' => $directory,
            'filename' => $filename
            ));

        return;

}

?>