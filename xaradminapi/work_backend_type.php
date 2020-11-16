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

function translations_adminapi_work_backend_type($args = null)
{
    static $type = null;
    if (isset($args['type'])) {
        $type = $args['type'];
    } elseif ($type == null) {
        $type = xarModVars::get('translations', 'work_backend_type');
    }
    return $type;
}
