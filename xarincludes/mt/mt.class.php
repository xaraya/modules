<?php

  // TODO: guess ;-)
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
include_once("mtrepo.class.php");

// Include the delta class
include_once("mtdelta.class.php");

// Include the changeset class
include_once("mtcset.class.php");

// Include the repository file class
include_once("mtfile.class.php");

?>