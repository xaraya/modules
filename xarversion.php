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
 *//**
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
 *
 * The "media-types" directory contains a subdirectory for each content
 * type and each of those directories contains a file for each content
 * subtype.
 *
 *                                |-application-
 *                                |-audio-------
 *                                |-image-------
 *                  |-media-types-|-message-----
 *                                |-model-------
 *                                |-multipart---
 *                                |-text--------
 *                                |-video-------
 *
 *    URL = ftp://ftp.isi.edu/in-notes/iana/assignments/media-types
 */

$modversion['name']         = 'uploads';
$modversion['id']           = '666';
$modversion['version']      = '1.1.0'; // long jump to current uploads_guimods version
$modversion['displayname']  = xarML('Uploads');
$modversion['description']  = 'Upload/Download File Handler';
$modversion['credits']      = 'docs/credits.txt';
$modversion['help']         = 'docs/help.txt';
$modversion['changelog']    = 'docs/changelog.txt';
$modversion['license']      = 'docs/license.txt';
$modversion['official']     = 1;
$modversion['author']       = 'Marie Altobelli (Ladyofdragons); Michael Cortez (mcortez); Carl P. Corliss (rabbitt)';
$modversion['contact']      = 'ladyofdragons@xaraya.com; mcortez@xaraya.com; rabbitt@xaraya.com';
$modversion['admin']        = 1;
$modversion['user']         = 0;
$modversion['class']        = 'Utility';
$modversion['category']     = 'Global';
$modversion['dependency']   = array(999);
?>
