<?php
/**
 * process date/time for the modified item - hook for ('item','update','API')
 *
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian development Team
 */
/**
 * process date/time for the modified item - hook for ('item','update','API')
 *
 * @author Jorn
 * @param array $args
 */
function julian_userapi_updatehook($args)
{
     // We may have been asked not to update (articles does this when changing article status).
    if (xarVarGetCached('Hooks.all','noupdate')) return;

    // We handle this with the create hook (which can update current records, too)
    return xarModAPIFunc('julian', 'user', 'createhook', $args);
}
?>
