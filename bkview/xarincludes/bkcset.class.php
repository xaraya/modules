<?php

// Class to model a bitkeeper changeset
class bkChangeSet  
{
    var $_repo;     // in which repository is this changeset?
    var $_rev;      // which changeset to instantiate?
    var $_deltas;   // array of file/rev combos which hold the deltas in this cset
    var $_author;   // author of this cset
    var $_comments; // cset comment
    var $_key;      // fixed key of this cset
    var $_tag;      // tag, if any
    var $_age;      // how old is this
    
    function bkChangeset($repo,$rev='+') 
   {
        $this->_repo=$repo;
        $this->_rev=$rev;   // changeset revision number
                            // Fill basic properties
        $cmd = "bk changes -r".$rev. " -d':P:\n\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}\n:KEY:\n:AGE:\n:TAG:'";
        $tmp = $this->_repo->_run($cmd);
        $this->_author = $tmp[0];
        $this->_comments = explode(BK_NEWLINE_MARKER,$tmp[1]);
        $this->_key = $tmp[2];
        $this->_age = $tmp[3];
        if(array_key_exists(4,$tmp)) {
            $this->_tag = $tmp[4];
        } else {
            $this->_tag = '';
        }
        
        // Fill delta array with identification of deltas
        $this->_deltas=NULL;
        $this->_deltas();
   }
    
    // Private function to initialize delta array
    function _deltas() 
   {
        $cmd="bk changes -vn -r".$this->_rev." -d':GFILE:|:REV:'";
        $tmp = $this->_repo->_run($cmd);
        while (list(,$did) = each($tmp)) {
            list($file,$rev) = explode('|',$did);
            if (strtolower($file)!="changeset") {
                $this->_deltas[$did]=new bkDelta($this,$file,$rev);
            }
        }
   }
    
    function bkDeltaList() 
   {
        return $this->_deltas;
   }
    
    
    function bkDeltas($formatstring="':GFILE:|:REV:'") 
   {
        $cmd="bk changes -vn -r".$this->_rev." -d$formatstring";
        return $this->_repo->_run($cmd);
   }
    
    function bkRev() 
   {
        return $this->_rev;
   }
    
    function bkGetAuthor()
   {
        return $this->_author;
   }
    
    function bkGetComments()
   {
        return $this->_comments;
   }
    
    function bkGetKey()
   {
        return $this->_key;
   }
    
    function bkGetTag()
   {
        return $this->_tag;
   }
    
    function bkGetAge()
   {
        return $this->_age;
   }
}
?>
