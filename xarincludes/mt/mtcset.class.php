<?php

include_once "modules/bkview/xarincludes/scmcset.class.php";
class mtChangeSet // In monotone a changeset is NOT a delta on the changeset file.
{
    var $repo = null; // To which repository object does this changeset belong?
    var $rev='';      // Changeset revision
    
    function mtChangeSet($repo, $rev='')
    {
        $this->file = 'ChangeSet';
        $this->rev = $rev;
        $this->repo =& $repo;
        $this->tag = $this->GetTag();
        $this->key = $this->GetKey();
        $this->author = $this->GetAuthor();
        $this->age = 'TDB';
        $this->comments = $this->getComments();
        
        $this->deltas = $this->DeltaList();

    }
    
    // Retrieve the tag for this revision, if any
    function GetTag()
    {
        $sql = "SELECT value 
                FROM revision_certs 
                WHERE name=? AND
                      id = ?";
        $result =& $this->repo->_dbconn->execute($sql,array('tag',$this->rev));
        if(!$result) return;
        if(!$result->EOF) {
            // There is a tag
            list($tag) = $result->fields;
            return base64_decode($tag);
        }
        return '';
    }
    
    function GetAuthor()
    {
        $sql = "SELECT value 
                FROM revision_certs 
                WHERE name=? AND
                      id = ?";
        $result =& $this->repo->_dbconn->execute($sql,array('author',$this->rev));
        if(!$result) return;
        if(!$result->EOF) {
            list($author) = $result->fields;
            return base64_decode($author);
        }
        return '';
    }
    
    function GetComments()
    {
        $sql = "SELECT value 
                FROM revision_certs 
                WHERE name=? AND
                      id = ?";
        $result =& $this->repo->_dbconn->execute($sql,array('changelog',$this->rev));
        if(!$result) return;
        if(!$result->EOF) {
            list($comments) = $result->fields;
            return nl2br(base64_decode($comments));
        }
        return '';
    }
    
    function GetKey()
    {
        return $this->rev;
    }
    
    function DeltaList()
    {
        // TODO: Guess!
        return array();
    }
}
?>