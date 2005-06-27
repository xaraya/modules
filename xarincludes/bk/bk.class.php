<?php

/**
 * Classes which together model bitkeeper repository items 
 *
 * @package modules
 * @copyright (C) 2004 The Digital Development Foundation, Inc.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

define('BK_SEARCH_REPO',   8);
define('BK_SEARCH_FILE',   4);
define('BK_SEARCH_CSET',   1);
define('BK_SEARCH_DELTAS', 2);

define('BK_FIELD_MARKER','|');
define('BK_NEWLINE_MARKER','<nl/>');

define('BK_FLAG_FORWARD'   ,  1);
define('BK_FLAG_SHOWMERGE' , 2);
define('BK_FLAG_TAGGEDONLY', 4);
define('BK_FLAG_NORANGEREVS', 8);


// Include the repository class
include_once("bkrepo.class.php");

// Include the delta class
include_once("bkdelta.class.php");

// Include the changeset class
include_once("bkcset.class.php");

// Include the repository file class
include_once("bkfile.class.php");

?>
