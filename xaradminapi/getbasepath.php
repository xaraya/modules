<?php
/**
 * Get the downloads basepath
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
 * Get the downloads basepath
 */
function downloads_adminapi_getbasepath() {

    include(sys::varpath(). '/config.system.php');

    if (!empty($systemConfiguration['downloads.basepath'])) {
        $basepath = $systemConfiguration['downloads.basepath'];
    } else {
        $basepath = '../';
    }

    return $basepath;

}

?>