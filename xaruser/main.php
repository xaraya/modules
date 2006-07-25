<?php
/**
 * xTasks Module - A simple task management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_user_main()
{
    if (!xarSecurityCheck('ViewXProject')){
        return;
    }

    $data = xarModAPIFunc('xtasks','user','menu');
    $data['welcome'] = xarML('Welcome to the xtasks module...');
    return $data;
}
?>