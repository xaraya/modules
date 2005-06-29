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
        // We have to get:
        // - age
        // - author
        // - revision
        // - comments
        // But how do we get from the filename to an ID without a working copy?
    }
}

?>