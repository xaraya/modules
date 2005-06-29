<?php

include_once "modules/bkview/xarincludes/scmdelta.class.php";
class mtDelta extends scmDelta
{

    function mtDelta($repo, $file, $rev)
    {
        $this->repo =& $repo;
        $this->file = $file;
        $this->rev = $rev;
        $this->author = "TBD"; // Same as cset author
        $this->age = "TBD"; // Same as cset age
        $this->comments = "TBD"; // No delta comments specifically for mt
    }
}