<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */

    // The following constants define overall status of a message
    // This schema is consistent with the oe we use in (almost) all modules
    define('MESSAGES_DELETED', 0);
    define('MESSAGES_ACTIVE',  1);

    define('MESSAGES_STATUS_DRAFT',  0);
    define('MESSAGES_STATUS_UNREAD', 1);
    define('MESSAGES_STATUS_READ',   2);

?>
