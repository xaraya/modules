<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_adminapi_make_package($args)
{
    extract($args);

    // Argument check
    assert('isset($basefilename) && isset($version) && isset($dirpath) && isset($locale)');

    if (!$archiver_path = xarMod::apiFunc('translations', 'admin', 'archiver_path')) {
        return;
    }
    if (!file_exists($archiver_path) || !is_executable($archiver_path)) {
        $msg = xarML('Cannot execute \'#(1)\'.', $archiver_path);
        throw new Exception($msg);
    }
    if (!$archiver_flags = xarMod::apiFunc('translations', 'admin', 'archiver_flags')) {
        return;
    }

    if (strpos($archiver_path, 'zip') !== false) {
        $ext = 'zip';
    } elseif (strpos($archiver_path, 'tar') !== false) {
        $ext = 'tar';
        if (strpos($archiver_flags, 'z') !== false) {
            $ext .= '.gz';
        } elseif (strpos($archiver_flags, 'j') !== false) {
            $ext .= 'bz2';
        }
    } else {
        $ext = 'unknown';
    }
    $filename = "$basefilename-{$version}_i18n-$locale.$ext";
    $filepath = sys::varpath().'/cache/'.$filename;

    $archiver_flags = str_replace('%f', $filepath, $archiver_flags);
    $archiver_flags = str_replace('%d', $dirpath, $archiver_flags);

    system("$archiver_path $archiver_flags");

    return $filename;
}
