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
        $sql = "SELECT data FROM revisions WHERE id=?";
        $result =& $this->repo->_dbconn->execute($sql,array($this->rev));
        if(!$result) return;
        
        $deltas = array();
        if(!$result->EOF) {
            // data is base64 encoded and gzipped
            list($data) = $result->fields;
            $data = base64_decode($data);
            // We dont want the gzip header here, strip it off, then uncompress
            $data = gzinflate(substr($data, 10));
            // At this point we have a larg string describing all the changes in this revision.
            // Kinda lame, but we need to parse this ourselves to get the information on the deltas
            // This is the format:
            // <!-- for a normal delta -->
            // patch "monotone.texi"
            //  from [6fb3fe1d81b2cc330e4dc04e54df634721817965]
            //    to [d5a3fc65738c06b2405df1245da00cf8195fddbc]
            // <!-- for a 1.0 delta -->
            // patch "tests/t_db_kill_branch_locally.at"
            //  from []
            //    to [dc67769b12aea0c26b6d4e3b2347c56ee99cde4b]
            // These blocks are separated by blank lines.
            //
            // We need to extract from this:
            // - the file
            // - the delta revision
            // and pass these to the delta class and construct a delta from it.
            // So, let's do some regex matching on the data
            $matches = array();
            $pattern = '/patch \"(.*)\"\n from \[(.*)\]\n   to \[(.*)\]/';
            $flags = PREG_SET_ORDER;
            if(preg_match_all($pattern, $data, $matches, $flags)) {
                foreach($matches as $match) {
                    $deltas[$match[3]] = new mtDelta($this->repo, $match[1], $match[3]);
                }
            }
        }
        return $deltas;
    }
}
?>