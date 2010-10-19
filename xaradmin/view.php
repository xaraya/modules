<?php
/**
 * contains the module information
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 * @return array
 */
function comments_admin_view()
{
    // Security Check
    if(!xarSecurityCheck('AdminComments')) {
        return;
    }

	$data['items'] = xarMod::apiFunc('comments','user','getitems');

    return xarTplModule('comments', 'admin', 'view', $data);

}
?>