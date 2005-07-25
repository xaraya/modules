<?php

/**
 * vdir_copy - copy a directory and all of it's children to another location
 *             When copying the files to the new location, the are only effectively
 *             'hard linked' to the new location from the old (they receive new fileId's
 *             but the xar_location points to the same file). They only become a distinctly
 *             seperate file once a change is made to either the original or the "hard linked
 *             clone."
 * @param   integer $src_id      ID of the directory to move
 * @param   integer $dest_id     ID of the directory to move the source directory to
 * @returns bool
 * @return  TRUE on success, FALSE otherwise
 */


function uploads_vdirapi_copy( $args )
{

}

?>
