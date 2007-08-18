<?php

include_once('modules/sitesearch/xarclass/engine.php');

include_once('modules/sitesearch/xarclass/libXapian.php');

class xapian_engine extends sitesearch_engine
{
    var $base = 'var/sitesearch/';

    //    Holds a xapian database object or a collection of them
    var $databases;

    var $result_set;

    //Contains search results ready for output
    var $results;

    //  Contains number of search results
    var $num_results;

    // Constructs the Xapian search engine
    function __construct($dbs=null)
    {
        $this->databases = new XapianDatabase();
        $db_path = xarModGetVar('sitesearch', 'database_path');
        
        if( is_null($dbs) ) 
            $dbs = $this->get_limits();

        foreach( $dbs as $db )
        {
            $path = $db_path . $db['database_name'];
            if( file_exists($path) )
                $this->databases->add_database(new XapianDatabase($path));
        }
    }

    /**
        Performs a search with keywords against the databases
    */
    function search($keywords, $start=0, $num=10)
    {
        xarLogMessage("SS: search started");
        if( is_null($this->databases) )
            return false; 
            

        $this->keywords = $keywords; // cached the keywords for later use
        parent::search($keywords);

        // Configure the stemmer
        // @todo: make this follow the site language? what if site is NL and content is EN? or vice versa?
        $stemmer = new  XapianStem ("none");

        // Configure the query parser
        $qp      = new XapianQueryParser();
        
        $qp->set_stemmer($stemmer);
        $qp->set_stemming_strategy(XapianQueryParser::STEM_ALL);
        $qp->set_database($this->databases);
        $qp->set_default_op(XapianQuery::OP_ELITE_SET);
        
        $query = $qp->parse_query($keywords,XapianQueryParser::FLAG_WILDCARD|XapianQueryParser::FLAG_PARTIAL);

        // Enquire object is used to actually perform the query
        // the query parser object returned
        $enq = new XapianEnquire($this->databases);
        $enq->set_query($query);

        /*
            Get the next set of records
        */
        $this->result_set = $enq->get_mset($start, $num);
        xarLogMessage("SS: Resultset: " . $this->result_set->size());
        
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
        
        $item = $this->result_set->begin();

        while( !$item->equals($this->result_set->end()))  
        {
            $document = $item->get_document();

            // Get the url and make a smaller display friendly url so that it does
            // screw up our layout.
            // NOTE: May want to make this configure via admin interface
            $url = $document->get_value(4);
            $display_url = substr($url, 0, 75);

            // Highlight individual words
            $text = $document->get_value(2);
            foreach( $words as $word )
            {
                $text = $this->highlight($text, $word);
            }

            $this->results[$i] = array(
               'text'        => $text,
               'url'         => $url,
               'display_url' => $display_url,
               'title'       => $document->get_value(1),
               'relevancy'   => $item->get_percent()
            );

            // Get the next document
            $item->next();
            $i++;
        }

        $this->num_results = $this->result_set->size();

        return true;
    }

    /**
        Gets the estimated number of matches
    */
    function get_num_matches()
    {
        if( is_null($this->databases) ){ return 0; }
        return $this->result_set->get_matches_estimated();
    }

    /**
        Get the number of documents in the database
    */
    function get_doc_count()
    {
        if( is_resource($this->databases) )
            return $this->databases->get_doccount();
        else
            return 0;
    }
}
?>
