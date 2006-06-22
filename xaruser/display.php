<?php
/**
 * External page entry point
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 * @author Yassen Yotov (CyberOto)
 * @author  Shawn McKenzie (AbraCadaver)
 */

/**
 * External page entry point
 *
 * @return  data on success or void on falure
 * @throws  XAR_SYSTEM_EXCEPTION, 'NOT_ALLOWED'
*/
function window_user_display($args)
{
    $data = array();
    if (isset($id)) {
        $data['hooks'] = xarModCallHooks('item', 'display', $id, array('itemtype'  => $id,
                                                                       'returnurl' => xarModURL('window', 'user', 'main', array('page' => $page, 'id' => $id))),
                                                                'window');
    }
    $data['title'] = "Xaraya Window";
    return $data;
}
?>