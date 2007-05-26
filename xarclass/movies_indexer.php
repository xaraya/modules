<?php

/**


*/
include_once("modules/sitesearch/xarclass/indexer.php");

class movies_indexer extends indexer 
{

    function movies_indexer($args=null)
    {
        $this->name = "movies";
        
        parent::indexer();
    
    }

    /**
        Query the DB and setup the record set
    */
    function get_items()
    {
        $table = xarDBGetSiteTablePrefix() . '_movies';
        $sql = "
            SELECT xar_movieId, xar_title, xar_synopsis100, xar_synopsis250
            FROM $table
        ";
        $this->record_set = $this->db_conn->Execute($sql);
        if( !$this->record_set ) return false;
        
        return true;
    }

    /*
        Generate entry from DB record set
    */
    function &make_document($fields)
    {
        list($id, $title, $short_summary, $long_summary) = $fields;

        $url = xarModURL('movies', 'user', 'movie', array('mid' => $id));
        
        if( !empty($long_summary) ){ $summary = $long_summary; }
        else { $summary = $short_summary; }

        $document = new_document();
        
        $this->index_text($title, $document, $weight=3);
        $this->index_text($summary, $document, $weight=2);
        
        /*
            Add values to documents for use in displaying search results
        */
        $i = 0;
        document_add_value($document, $i++, $id);
        document_add_value($document, $i++, $title);
        document_add_value($document, $i++, substr(strip_tags(trim($summary)), 0, 255));
        document_add_value($document, $i++, 'HTML');
        document_add_value($document, $i, $url);        
        
        writabledatabase_replace_document($this->database, $id, $document);
        
        return $document;    
    }
}
?>