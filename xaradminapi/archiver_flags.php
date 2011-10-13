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

function translations_adminapi_archiver_flags($args = NULL)
{
    static $archiver_flags = NULL;
    if (isset($args['archiver_flags'])) {
        $archiver_flags = $args['archiver_flags'];
    } elseif ($archiver_flags == NULL) {
        $archiver_flags = xarModVars::get('translations', 'archiver_flags');
    }
    return $archiver_flags;
}

?>