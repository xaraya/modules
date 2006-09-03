<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    generate menu fragments
    @param $args['page'] - func calling menu (ex. main, view, etc)
    @return Menu template data
 */
function helpdesk_user_menu()
{
    if( !xarVarFetch('func',      'str', $data['page'],       'main', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('selection', 'str', $data['selection'],  '',     XARVAR_NOT_REQUIRED) ){ return false; }
    $data['menulinks'] = xarModAPIFunc('helpdesk', 'user', 'getmenulinks');
    return xarTplModule('helpdesk', 'user', 'menu', $data);
}
?>
