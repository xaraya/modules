<?php

/**


*/
include_once("modules/sitesearch/xarclass/indexer.php");

class movies_indexer extends indexer 
{
    var $name = 'movies';
    
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

        $document = new XapianDocument();
        
        $this->index_text($title, $document, $weight=3);
        $this->index_text($summary, $document, $weight=2);
        
        /*
            Add values to documents for use in displaying search results
        */
        $i = 0;
        $document->add_value($i++, $id);
        $document->add_value($i++, $title);
        $document->add_value($i++, substr(strip_tags(trim($summary)), 0, 255));
        $document->add_value($i++, 'HTML');
        $document->add_value($i, $url);        
        
        $this->database->replace_document((int) $id, $document);
        return $document;    
    }
}
?>