<?php
/**
 * isformantiboted function
 *
 * @package Modules
 * @copyright (C) 2002-2006 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage formantibot
 * @link http://xaraya.com/index.php/release/761.html
 */

/**
 * Verifies whether a domain is formantiboted or not
 *
 * @access public
 * @param string $domain Domain to verify for formantibot status
 * @returns bool True if formantiboted, false otherwise
 *
 */
function formantibot_userapi_validatenum($args)
{
    include_once 'modules/formantibot/xarclass/securelogic.php';

    extract($args);

    if (isset($userInput)) {
        $secureLogic = new securlogic();
        return $secureLogic->validate($userInput);
    }
    return FALSE;

}
?>
