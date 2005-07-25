<?php

/* File: $Id
 * ----------------------------------------------------------------------
 * Xaraya eXtensible Management System
 * Copyright (C) 2002 by the Xaraya Development Team.
 * http://www.xaraya.org
 * ----------------------------------------------------------------------
 * Original Author of file: Carl P. Corliss (ccorliss@schwabfoundation.org)
 * Purpose of file:  uploads user API
 * ----------------------------------------------------------------------
 */

define('_UPLOADS_STORE_DB_ENTRY',       1<<0);
define('_UPLOADS_STORE_FILESYSTEM',     1<<1);
define('_UPLOADS_STORE_DB_DATA',        1<<2);
define('_UPLOADS_STORE_TEXT',           1<<3);
define('_UPLOADS_STORE_DB_FULL',        _UPLOADS_STORE_DB_ENTRY   | _UPLOADS_STORE_DB_DATA);
define('_UPLOADS_STORE_FSDB',           _UPLOADS_STORE_FILESYSTEM | _UPLOADS_STORE_DB_ENTRY);

define('_UPLOADS_STATUS_SUBMITTED',     0x01);      // File has been recently submitted and needs approving
define('_UPLOADS_STATUS_APPROVED',      0x02);      // File has been approved and is ready for system use
define('_UPLOADS_STATUS_REJECTED',      0x03);      // File has been rejected and needs deleting

define('_INODE_TYPE_P_DIRECTORY',       0x01);      // Inode is the previous directory
define('_INODE_TYPE_C_DIRECTORY',       0x02);      // Inode is the current directory
define('_INODE_TYPE_DIRECTORY',         0x03);      // Inode is a directory
define('_INODE_TYPE_FILE',              0x04);      // Inode is a file
define('_INODE_TYPE_LINK',              0x05);      // Inode is a link (symbolic or otherwise)

define('_UPLOADS_ERROR_UNKNOWN',      -(0x01));     // Unidentifiable error
define('_UPLOADS_ERROR_NONE',           0x00);      // No error
define('_UPLOADS_ERROR_NO_OBFUSCATE',   0x01);      // Unable to obfuscate the filename
define('_UPLOADS_ERROR_BAD_FORMAT',     0x02);      // Incorrect DATA structure
define('_UPLOADS_ERROR_NOT_EXIST',      0x03);      // non-existent file
define('_UPLOADS_ERROR_INVALID_PATH',   0x04);      // non-existent file

define('_UPLOADS_GET_UPLOAD',           0x01);
define('_UPLOADS_GET_EXTERNAL',         0x02);
define('_UPLOADS_GET_EXT_FTP',          0x03);
define('_UPLOADS_GET_EXT_HTTP',         0x04);
define('_UPLOADS_GET_LOCAL',            0x05);
define('_UPLOADS_GET_REFRESH_LOCAL',    0x06);
define('_UPLOADS_GET_STORED',           0x07);
define('_UPLOADS_GET_NOTHING',          0x08);

define('_UPLOADS_APPROVE_NOONE',        0x01);
define('_UPLOADS_APPROVE_EVERYONE',     0x02);
define('_UPLOADS_APPROVE_ADMIN',        0x03);

define('_UPLOADS_VDIR_ROOTFS',          1<<0);
define('_UPLOADS_VDIR_USERS',           1<<1);
define('_UPLOADS_VDIR_PUBLIC',          1<<2);
define('_UPLOADS_VDIR_TRASH',           1<<3);
define('_UPLOADS_VDIR_ALL',             _UPLOADS_VDIR_ROOTFS | _UPLOADS_VDIR_USERS |
                                        _UPLOADS_VDIR_PUBLIC | _UPLOADS_VDIR_TRASH);

define('_UPLOADS_VDIR_SORTBY_SIZE',     'size');
define('_UPLOADS_VDIR_SORTBY_NAME',     'name');
define('_UPLOADS_VDIR_SORTBY_TYPE',     'type');
define('_UPLOADS_VDIR_SORTBY_OWNER',    'owner');
define('_UPLOADS_VDIR_SORTBY_DATE',     'date');

define('_UPLOADS_VDIR_SORT_ASC',        'asc');
define('_UPLOADS_VDIR_SORT_DESC',       'desc');

?>
