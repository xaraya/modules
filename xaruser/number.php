<?php
/**
 * Generate a number logic
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008,2009 2skies.com
 * @link http://xarigami.com/project/formantibot
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
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
