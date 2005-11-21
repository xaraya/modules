<?php

include_once "modules/bkview/xarincludes/scmfile.class.php";

class mtFile extends scmFile
{
    
    function mtFile($repo, $file)
    {
        $this->repo =& $repo;
        $this->file = $file;
    }

    function AbsoluteName()
    {
        return $this->file;
    }
    
    function History($user='')
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