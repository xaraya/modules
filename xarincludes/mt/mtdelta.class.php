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
    
    function Diffs()
    {
        $sql = "SELECT delta FROM file_deltas WHERE id = ?";
        $result =& $this->repo->_dbconn->execute($sql,array($this->rev));
        if(!$result) return;
        $delta = '';
        if(!$result->EOF) {
            list($delta) = $result->fields;
            // base64, gzip, blah blah, the usual
            $delta = base64_decode($delta);
            $delta = gzinflate(substr($delta,10));
        }
        // The delta is an rdiff (can we convert that to something readable?)
        // Make sure its a line by line array
        $lines = explode("\n",$delta);
        return $lines;
    }
}
?>