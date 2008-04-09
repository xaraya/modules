<?php
/**
 * Get work backend type
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_adminapi_work_backend_type($args = NULL)
{
    static $type = NULL;
    if (isset($args['type'])) {
        $type = $args['type'];
    } elseif ($type == NULL) {
        $type = xarModVars::get('translations', 'work_backend_type');
    }
    return $type;
}

?>