<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

    function sitemapper_admin_template()
    {
        if (!xarSecurityCheck('EditSitemapper')) return;

        if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

        if ($data['confirm']) {
            // Get the data from the form
            if (!xarVarFetch('template',       'str',    $data['template'], '', XARVAR_NOT_REQUIRED)) return;
            xarModVars::set('sitemapper','template', $data['template']);
        } else {
            $data['template'] = xarModVars::get('sitemapper','template');
        }
        return $data;
    }
?>