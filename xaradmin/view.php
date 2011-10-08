<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */

/**
 * show the links for module items
 * @return array
 */
function keywords_admin_view($args)
{
    extract($args);

    if (!xarVarFetch('modid',    'id', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'id', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminKeywords')) return;

    $data = array();

    return $data;
}

?>
