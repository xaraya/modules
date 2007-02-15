<?php
/**
 * Uploads user API
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */

/*
 * Original Author of file: Marie Altobelli (Ladyofdragons)
 */

define('_UPLOADS_STORE_DB_ENTRY',     1<<0);
define('_UPLOADS_STORE_FILESYSTEM',   1<<1);
define('_UPLOADS_STORE_FSDB',         _UPLOADS_STORE_FILESYSTEM | _UPLOADS_STORE_DB_ENTRY);
define('_UPLOADS_STORE_DB_DATA',      1<<2);
define('_UPLOADS_STORE_DB_FULL',      _UPLOADS_STORE_DB_ENTRY | _UPLOADS_STORE_DB_DATA);
define('_UPLOADS_STORE_TEXT',         1<<3);
define('_UPLOADS_LOCATION_TRUSTED',   1<<4);
define('_UPLOADS_LOCATION_UNTRUSTED', 1<<5);
define('_UPLOADS_LOCATION_OTHER',     1<<6);

define('_UPLOADS_STATUS_SUBMITTED', 1);      // File has been recently submitted and needs approving
define('_UPLOADS_STATUS_APPROVED',  2);      // File has been approved and is ready for system use
define('_UPLOADS_STATUS_REJECTED',  3);      // File has been rejected and needs deleting

define('_INODE_TYPE_P_DIRECTORY', 1);      // Inode is the previous directory
define('_INODE_TYPE_C_DIRECTORY', 2);      // Inode is the current directory
define('_INODE_TYPE_DIRECTORY',   3);      // Inode is a directory
define('_INODE_TYPE_FILE',        4);      // Inode is a file
define('_INODE_TYPE_LINK',        5);      // Inode is a link (symbolic or otherwise)

define('_UPLOADS_ERROR_UNKNOWN',     -1); // Unidentifiable error
define('_UPLOADS_ERROR_NONE',         0); // No error
define('_UPLOADS_ERROR_NO_OBFUSCATE', 1); // Unable to obfuscate the filename
define('_UPLOADS_ERROR_BAD_FORMAT',   2); // Incorrect DATA structure
define('_UPLOADS_ERROR_NOT_EXIST',    3); // non-existent file

define('_UPLOADS_GET_UPLOAD',        1);
define('_UPLOADS_GET_EXTERNAL',      2);
define('_UPLOADS_GET_EXT_FTP',       3);
define('_UPLOADS_GET_EXT_HTTP',      4);
define('_UPLOADS_GET_LOCAL',         5);
define('_UPLOADS_GET_REFRESH_LOCAL', 6);
define('_UPLOADS_GET_STORED',        7);
define('_UPLOADS_GET_NOTHING',       8);

define('_UPLOADS_APPROVE_NOONE',     1);
define('_UPLOADS_APPROVE_EVERYONE',  2);
define('_UPLOADS_APPROVE_ADMIN',     3);

?>