<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2018 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * View cache files
 *
 */
function cacher_admin_view_files($args)
{
    if (!xarSecurityCheck('ManageCacher')) return;

    if(!xarVarFetch('cache',     'int',  $data['cache'],      0, XARVAR_NOT_REQUIRED)) {return;}

    return $data;
}
?>