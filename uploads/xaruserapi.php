<?php

/* File: $Id
 * ----------------------------------------------------------------------
 * Xaraya eXtensible Management System
 * Copyright (C) 2002 by the Xaraya Development Team.
 * http://www.xaraya.org
 * ----------------------------------------------------------------------
 * Original Author of file: Marie Altobelli (Ladyofdragons)
 * Purpose of file:  uploads user API
 * ---------------------------------------------------------------------- 
 */ 
 
define('_UPLOADS_STORE_DB_ENTRY',   1);
define('_UPLOADS_STORE_FILESYSTEM', 2);
define('_UPLOADS_STORE_FSDB',       _UPLOADS_STORE_FILESYSTEM | _UPLOADS_STORE_DB_ENTRY);
define('_UPLOADS_STORE_DB_DATA',    4);
define('_UPLOADS_STORE_DB_FULL',    _UPLOADS_STORE_DB_ENTRY | _UPLOADS_STORE_DB_DATA);
define('_UPLOADS_STORE_TEXT',       8);

define('_UPLOADS_STATUS_SUBMITTED',1);
define('_UPLOADS_STATUS_APPROVED',2);
define('_UPLOADS_STATUS_REJECTED',3);

define('_INODE_TYPE_DIRECTORY', 1);
define('_INODE_TYPE_FILE', 2);
define('_INODE_TYPE_LINK', 3);

?>
