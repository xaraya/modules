<?php

/**


*/
include_once("modules/sitesearch/xarclass/indexer.php");

class articles_indexer extends indexer 
{
    
    var $db_fields = array(
        'xar_aid' => 'id',
        'xar_title' => 'title',
        'xar_summary' => 'summary',
        'xar_pubtypeid' => 'ptid'
    );
    
    
    function articles_indexer($args=null)
    {
        if( isset($args['database_name']) )
            $this->name = $args['database_name'];        
        else 
            $this->name = "articles";

        parent::indexer();    

        if( isset($args['args']) )
            $this->args = $args['args'];
        if( isset($args['mappings']) )
            $this->db_fields = array_merge($args['mappings'], $this->db_fields);   
    }

    /**
        Query the DB and setup the record set
    */
    function get_items()
    {
        $table = xarDBGetSiteTablePrefix() . '_articles';
        $fields = array_keys($this->db_fields);     

        $from = '';
        $where[] = ' xar_status IN ( 2, 3 ) ';
        foreach( $this->args as $key => $value )
        {
            if( $key == 'itemtype' )
            {
                if( is_int($value) )
                {
                    $where[] = " xar_pubtypeid = $value ";
                }
                else 
                {
                    $pubtypes = xarModAPIFunc('articles', 'user', 'getpubtypes');
                    foreach( $pubtypes as $ptid => $pubtype )
                    {
                        if( $value == $pubtype['name'] )
                        {
                            $where[] = " xar_pubtypeid = $ptid ";
                            
                            break;                
                        }	
                    }
                }                
            }
            else if( $key == 'cids' ) 
            {
                if( strpos($value, ',') )
                    $cids = split(',', $value);
                else 
                    $cidtree = $value;
                
                // Load API
                if (!xarModAPILoad('categories', 'user')) return;
                $xartables = xarDBGetTables();
                
                // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from categories
                $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                    array(
                        'cids' => isset($cids) ? $cids : null,
                        'cidtree' => isset($cidtree) ? $cidtree : null,
                        'itemtype' => isset($ptid) ? $ptid : null,
                        'modid' => xarModGetIDFromName('articles')
                    )
                );
                //var_dump($categoriesdef);
                if (empty($categoriesdef)) return;
                
                //$this->db_fields[] = 'cid';
                //$fields[] = "{$categoriesdef['table']}.xar_cid";
                // Add the LEFT JOIN ... ON ... parts from categories
                $from .= ' LEFT JOIN ' . $categoriesdef['table'];
                $from .= ' ON ' . $categoriesdef['field'] . ' = ' . 'xar_aid';
                // Add the LEFT JOIN ... ON ... parts from categories
                $from .= " LEFT JOIN {$xartables['categories']} ";
                $from .= " ON {$xartables['categories']}.xar_cid = {$categoriesdef['table']}.xar_cid";
                $where[] = $categoriesdef['where'];             
            }
        }
        
        $sql  = " SELECT DISTINCT " . join(', ', $fields);
        $sql .= " FROM $table ";
        $sql .= " $from "; // Left join stuff
        $sql .= " WHERE " . join(" AND ", $where);
        $sql .= " ORDER BY xar_aid DESC ";

        $this->record_set = $this->db_conn->Execute($sql);
        if( !$this->record_set ) return false;
        
        $this->db_conn->Close();        
        
        return true;
    }

    function &make_document(&$fields)
    {
        // Proccesses fields returned and maps the fields to vars xapian is excepting.
        $x_fields = array_values($this->db_fields);        
        foreach( $fields as $key => $field )
        {
            if( isset($$x_fields[$key]) ){ $$x_fields[$key] .= " " . $field; }
            else {$$x_fields[$key] = $field; }
        }
        
        // Cleans the summary
        $summary = html_entity_decode(strip_tags(trim($summary)));

        $document = new_document();        
        $this->index_text($title, $document, $weight=3);
        $this->index_text($summary, $document, $weight=2);
        if( !empty($cid) )
        {
            //var_dump($cid);
            $this->index_text($cid, $document, $weight=1, 'XCAT');
        }
        
        /*
            Add values to documents for use in displaying search results
        */
        $i = 0;
        document_add_value($document, $i++, $id);
        document_add_value($document, $i++, $title);
        document_add_value($document, $i++, substr($summary, 0, 255));
        document_add_value($document, $i++, 'HTML');
        $url = xarModURL('articles', 'user', 'display', array('aid' => $id, 'ptid' => $ptid));
        document_add_value($document, $i, $url);        
        
        writabledatabase_replace_document($this->database, $id, $document);
        
        return $document;    
    }    
}
?>