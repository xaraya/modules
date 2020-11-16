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

function translations_adminapi_archiver_path($args = null)
{
    static $archiver_path = null;
    if (isset($args['archiver_path'])) {
        $archiver_path = $args['archiver_path'];
    } elseif ($archiver_path == null) {
        $archiver_path = xarModVars::get('translations', 'archiver_path');
    }
    return $archiver_path;
}
