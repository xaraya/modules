<?php

include_once "modules/bkview/xarincludes/scmdelta.php";
class mtnDelta extends scmDelta
{

    function __construct($repo, $file, $rev)
    {
        $this->repo =& $repo;
        $this->file = $file;
        $this->rev = $rev;
        $this->author = "TBD"; // Same as cset author
        $this->age = "TBD"; // Same as cset age
        $this->comments = "TBD"; // No delta comments specifically for mt
    }
    
    function diffs()
    {
        $filearg='';
        if($this->file != 'ChangeSet') {
            // File diff DOESNT WORK thru mtn interface, do it from the db directly
            $cmd="db execute \"SELECT delta FROM file_deltas WHERE base='".$this->rev."'\"";
            $result =& $this->repo->_run($cmd);
            array_shift($result);
            $result = join("\n",$result);
            $result = base64_decode($result);
            $result = gzinflate(substr($result,10));
        } else {
            $cmd = 'diff -r '.$csetrev. ' ' . $filearg;
            echo $cmd;
            $result =& $this->repo->_run($cmd);
        }
        // The delta is an rdiff (can we convert that to something readable?)
        // Make sure its a line by line array
        $lines = explode("\n",$result);
        return $lines;
    }
}
?>
