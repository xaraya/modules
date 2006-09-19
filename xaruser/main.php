<?php
/**
 * xTasks Module - Project ToDo management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_user_main()
{
    if (!xarSecurityCheck('ViewXProject')){
        return;
    }

    $data = xarModAPIFunc('xtasks','admin','menu');
    $data['welcome'] = xarML('Welcome to the xTasks module. ');
    return $data;
}
?>