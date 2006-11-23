<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Creates a file on the filesystem in the specified location with
 *  the specified contents.and adds an entry to the new file in the
 *  file_entry table after creations. Note: you must test specifically
 *  for false if you are creating a ZERO BYTE file, as this function
 *  will return zero for that file (ie: !== FALSE as opposed to != FALSE).
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string  filename       The name of the file (minus any path information)
 *  @param   string  fileLocation   The complete path to the file including the filename (obfuscated if so chosen)
 *  @param   string  mime_type      The mime content-type of the file
 *  @param   string  contents       The contents of the new file
 *
 *  @return integer The fileId of the newly created file, or ZERO (FALSE) on error
 */

function uploads_userapi_file_create( $args )
{

}

?>