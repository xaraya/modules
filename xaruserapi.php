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
    define('MESSAGES_STATUS_DELETED',   0);
    define('MESSAGES_STATUS_INACTIVE',  1);
    define('MESSAGES_STATUS_DRAFT',     2);
    define('MESSAGES_STATUS_ACTIVE',    3);

    // The following constants define whether a message can be seen by the sender or recipient or both
    // In effect this is a differentiated delete flag
    define('MESSAGES_DELETE_STATUS_DELETED',       0);
    define('MESSAGES_DELETE_STATUS_VISIBLE_TO',    1);
    define('MESSAGES_DELETE_STATUS_VISIBLE_FROM',  2);
    define('MESSAGES_DELETE_STATUS_ACTIVE',        3);
?>