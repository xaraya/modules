<?php
/**
 * isformantiboted function
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008,2009 2skies.com
 * @link http://xarigami.com/project/formantibot
 * @author Jo Dalle Nogare <icedlava@2skies.com>
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
