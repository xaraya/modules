<?php

/**


*/

include_once("modules/sitesearch/xarclass/indexer.php");

class julian_indexer extends indexer 
{
    var $name = 'julian';

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
        
        $document = new XapianDocument();
        
        $this->index_text($title, $document, $weight=3);
        $this->index_text($description, $document, $weight=2);
        
        /*
            Add values to documents for use in displaying search results
        */
        $i = 0;
        $document->add_value($i++, $id);
        $document->add_value($i++, $title);
        $document->add_value($i++, substr(strip_tags(trim($description)), 0, 255));
        $document->add_value($i++, 'HTML');
        $document->add_value($i, $url);        
        
        $this->database->replace_document((int) $id, $document);
        
        return $document;    
    }
}
?>