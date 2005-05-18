<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

  //! Write out serialized data.
  //  write_cache uses serialize() to store $var in $filename.
  //  $var      -  The variable to be written out.
  //  $filename -  The name of the file to write to.
  function write_cache(&$var, $filename) {
    $filename = DIR_FS_CACHE . $filename;
    $success = false;

    // try to open the file
    if ($fp = @fopen($filename, 'w')) {
      // obtain a file lock to stop corruptions occuring
      flock($fp, 2); // LOCK_EX
      // write serialized data
      fputs($fp, serialize($var));
      // release the file lock
      flock($fp, 3); // LOCK_UN
      fclose($fp);
      $success = true;
    }

    return $success;
  }
?>