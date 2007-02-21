<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
define('SECURITY_NONE', 0);
define('SECURITY_OVERVIEW', 32);  // Used with left joins in getalls by deafult
define('SECURITY_READ', 16);      // Used when displaying
define('SECURITY_COMMENT', 8);    // Link
define('SECURITY_WRITE', 4);      // Change / Modify / Delete
define('SECURITY_MANAGE', 2);     // Not really sure how this helps yet
define('SECURITY_ADMIN',1);       // Change who can access an object (future)

include_once("modules/security/xarclass/settings.php");
include_once("modules/security/xarclass/security.php");

?>