<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * the main administration function - pass-thru
 */
function xproject_groups_main()
{
    $output = new xarHTML();

    // auth check
    if (!xarSecAuthAction(0, 'Groups::', '::', ACCESS_DELETE)) {
        $output->Text(_GROUPSNOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
    if(($func == "main" || empty($func))) $output->Text(xarModAPIFunc('xproject','user','menu'));

    return $output->GetOutput();
}

?>