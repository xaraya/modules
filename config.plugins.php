<?php
/**
 * A place for plugin config settings
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com

 * @subpackage CKEditor Module
 * @link http://www.xaraya.com/index.php/release/eid/1166
 * @author Marc Lutolf <mfl@netspan.ch> and Ryan Walker <ryan@webcommunicate.net>
 */

    $config['PGRFileManager.rootPath'] = '/home/mikespub/xaraya-core/html/var/uploads';

    $config['PGRFileManager.urlPath'] = 'https://owncloud.mikespub.net/bermuda/var/uploads';

    $config['PGRFileManager.allowedExtensions'] = 'pdf, txt, rtf, jpg, gif, jpeg, png';  //'' means all files

    $config['PGRFileManager.imagesExtensions'] = 'jpg, gif, jpeg, png, bmp';

    $config['PGRFileManager.fileMaxSize'] = 1024 * 1024 * 10; // bytes

    $config['PGRFileManager.imageMaxHeight'] = 724;

    $config['PGRFileManager.imageMaxWidth'] = 1280;

    $config['PGRFileManager.allowEdit'] = 'true';

    // This is from an attempt to allow different paths for different objects...
    /*if (!empty($prop)) {

        //print $prop;
        $pluginsConfiguration[$prop]['PGRFileManager.rootPath'] = '/home/mikespub/xaraya-core/html/var/uploads';

        $pluginsConfiguration[$prop]['PGRFileManager.urlPath'] = 'https://owncloud.mikespub.net/bermuda/var/uploads';

    } else {

        $pluginsConfiguration['default']['PGRFileManager.rootPath'] = '/home/mikespub/xaraya-core/html/var/uploads';

        $pluginsConfiguration['default']['PGRFileManager.urlPath'] = 'https://owncloud.mikespub.net/bermuda/var/uploads';
    }*/
