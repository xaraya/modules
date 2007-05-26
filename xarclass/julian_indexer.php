<?php

/**


*/

include_once("modules/sitesearch/xarclass/indexer.php");

class julian_indexer extends indexer 
{

    function julian_indexer($args=null)
    {
        $this->name = "julian";
        
        parent::indexer();
    
    }

    /**
        Query the DB and setup the record set
    */
    function get_items()
    {
        $table = xarDBGetSiteTablePrefix() . '_julian_events';
        $sql = "
            SELECT event_id, summary, description
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
        list($id, $title, $description) = $fields;

        $url = xarModURL('julian', 'user', 'viewevent', 
            array(
                'event_id' => $id,
                'cal_date' => date('Ymd')
            )
            
        );
        
        $document = new_document();
        
        $this->index_text($title, $document, $weight=3);
        $this->index_text($description, $document, $weight=2);
        
        /*
            Add values to documents for use in displaying search results
        */
        $i = 0;
        document_add_value($document, $i++, $id);
        document_add_value($document, $i++, $title);
        document_add_value($document, $i++, substr(strip_tags(trim($description)), 0, 255));
        document_add_value($document, $i++, 'HTML');
        document_add_value($document, $i, $url);        
        
        writabledatabase_replace_document($this->database, $id, $document);
        
        return $document;    
    }
}
?>