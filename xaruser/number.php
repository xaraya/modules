<?php
/**
 * Generate a number logic
 *
 * @package Modules
 * @copyright (C) 2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage formantibot
 * @link http://xaraya.com/index.php/release/761.html   
 */

/**
 * @access public
 * @param string $domain Domain to verify for formantibot status
 * @returns bool True if formantiboted, false otherwise
 *
 */
function formantibot_user_number()
{

    include_once 'modules/formantibot/xarclass/securelogic.php';

    $secureLogic = new securlogic();

    $foo = $secureLogic->display();
    return $foo;
}
?>
