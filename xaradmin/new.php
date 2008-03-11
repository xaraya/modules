<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
function headlines_admin_new()
{

    // Security Check
    if(!xarSecurityCheck('AddHeadlines')) return;
    $item = array();

    $item['module'] = 'headlines';
    $item['itemtype'] = NULL; // forum
    $hooks = xarModCallHooks('item','new','',$item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['url'] = '';
    $data['title'] = '';
    $data['desc'] = '';
    $data['submitlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();

    // Return the output
    return $data;
}
?>
