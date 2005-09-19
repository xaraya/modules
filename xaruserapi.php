<?php
/**
    Security - Provides unix style privileges to xaraya items.
 
    @copyright (C) 2003-2005 by Envision Net, Inc.
    @license GPL (http://www.gnu.org/licenses/gpl.html)
    @link http://www.envisionnet.net/
    @author Brian McGilligan <brian@envisionnet.net>
 
    @package Xaraya eXtensible Management System
    @subpackage Security module
*/

define('SECURITY_NONE', 0);
define('SECURITY_OVERVIEW', 32);  // Used with left joins in getalls by deafult
define('SECURITY_READ', 16);      // Used when displaying 
define('SECURITY_COMMENT', 8);    // Link
define('SECURITY_WRITE', 4);      // Change / Modify / Delete
define('SECURITY_MANAGE', 2);     // Not really sure how this helps yet 
define('SECURITY_ADMIN',1);       // Change who can access an object (future)

?>