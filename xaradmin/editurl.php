<?php
/**
 * Window Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Window Module
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Window Module Development Team
 */
function window_admin_editurl($args)
{
    if (!xarSecurityCheck('AdminWindow')) return;

    $data = array();
    $data['authid'] = xarSecGenAuthKey('window');
    $data['action'] = xarModURL('window', 'admin', 'addurl');
    $data['window_status'] = "edit";

    $data['urls'] = xarModAPIFunc('window','admin','geturls');

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if (!xarVarFetch('id', 'id', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bluff', 'str', $bluff, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    //Get the data for this url from the database

    $urlitem = xarModAPIFunc('window','user','get',array('itemid'=>$id));

    $data['host'] = $urlitem['name'];
    $data['alias'] = $urlitem['alias'];
    $data['id'] = $urlitem['itemid'];
    $data['lang_action'] = xarML("Save");

    $data['reg_user_only'] = $urlitem['reg_user_only'];
    $data['open_direct'] = $urlitem['open_direct'];
    $data['use_fixed_title'] = $urlitem['use_fixed_title'];
    $data['auto_resize'] = $urlitem['auto_resize'];
    $data['vsize'] = $urlitem['vsize'];
    $data['hsize'] = $urlitem['hsize'];

    return xarTplModule('window','admin','newurl',$data);
}
?>