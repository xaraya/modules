<?php

include_once "modules/bkview/xarincludes/scmfile.php";

class mtnFile extends scmFile
{
    
    function __construct($repo, $file)
    {
        $this->repo =& $repo;
        $this->file = $file;
    }

    function absoluteName()
    {
        return $this->file;
    }
    
    function history($user='')
    {
        // We have: the filename
        // We need:
        // - list of changes to that file and for each change:
        //   - age
        //   - author
        //   - revision
        //   - comments
        // But how do we get from the filename to an ID without a working copy?
        return array();
    }
}
?>
