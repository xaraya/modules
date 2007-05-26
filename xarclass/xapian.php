<?php

include_once('modules/sitesearch/xarclass/engine.php');

if( !extension_loaded('xapian') ) 
{
    dl("xapian.so");
}

class xapian extends sitesearch_engine 
{
    /**
    
    */
    var $base = 'var/sitesearch/';
    
    /**
        Holds a xapian database object or a collection of them
    */
    var $databases;
    
    /**
    
    */
    var $result_set;
    
    /**
        Contains search results ready for output
    */
    var $results;
    
    /**
        Contains number of search results
    */
    var $num_results;
    
    /**
        Constructs the Xapian search engine
    */
    function xapian($dbs=null)
    {
        $db_path = xarModGetVar('sitesearch', 'database_path');

        if( is_null($dbs) )        
            $dbs = $this->get_limits();
        
        foreach( $dbs as $db )
        {
            $path = $db_path . $db['database_name'];
            if( file_exists($path) )
            {
                if( empty($this->databases) )
                    $this->databases = new_database($path);
                else 
                    database_add_database($this->databases, new_database($path));
            }
        }
    }
    
    /**
        Performs a search with keywords against the databases
    */
    function search($keywords, $start=0, $num=10)
    {
        if( is_null($this->databases) ){ return false; }
        $this->keywords = $keywords; // cached the keywords for later use
        parent::search($keywords);
        
        /*
            Use Xapian's Query Parser Class to parse the users keywords
            We still need to work on the options as there are tons of options
        */
        $stemmer = new_stem ("english");        
        $query_parser = new_queryparser();

        // Set the stemmer and turn on the stemming strategy        
        queryparser_set_stemmer ($query_parser, $stemmer);
        queryparser_set_stemming_strategy ($query_parser, STEM_ALL);
        queryparser_set_database($query_parser, $this->databases);        
        queryparser_set_default_op($query_parser, OP_ELITE_SET );        
        $query = queryparser_parse_query($query_parser, $keywords, FLAG_WILDCARD);              
        
        // Enquire object is used to actually perform the query 
        // the query parser object returned
        $enq = new_enquire( $this->databases );
        enquire_set_query( $enq, $query );
        
        /*  
            Get the next set of records
        */
        $this->result_set = enquire_get_mset( $enq, $start, $num );
        
        $this->process_result_set();
        
        return true;
    }    
    
    /**
        Process search results into a usable form
    */
    function process_result_set()
    {
        if( is_null($this->databases) ){ return false; }
        $words = str_word_count($this->keywords, 1);
        
        $i = 0;
        $this->results = array();
        $item = mset_begin( $this->result_set );
        while ( !msetiterator_equals($item, mset_end($this->result_set)) ) 
        {
            $document = msetiterator_get_document( $item );
            
            // Get the url and make a smaller display friendly url so that it does
            // screw up our layout.
            // NOTE: May want to make this configure via admin interface
            $url = document_get_value( $document, 4 );
            $display_url = substr($url, 0, 75);
            
            // Highlight individual words
            $text = document_get_value( $document, 2 );
            foreach( $words as $word ) 
            {
                $text = $this->highlight($text, $word);           
            }
            
        	$this->results[$i] = array(
        	   'text'        => $text,
        	   'url'         => $url,
        	   'display_url' => $display_url,
        	   'title'       => document_get_value( $document, 1 ),
        	   'relevancy'   => msetiterator_get_percent( $item )
        	);
        	        
        	// Get the next document		 
            msetiterator_next($item);
            $i++;
        }
        
        $this->num_results = mset_size($this->result_set);
        
        return true;
    }
    
    /**
        Gets the estimated number of matches
    */
    function get_num_matches()
    {
        if( is_null($this->databases) ){ return 0; }
        return mset_get_matches_estimated($this->result_set);
    }
    
    /**
        Get the number of documents in the database
    */
    function get_doc_count()
    {
        if( is_resource($this->databases) )
            return database_get_doccount($this->databases);
        else
            return 0;
    }    
}
?>
