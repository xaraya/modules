<?php

include_once("modules/sitesearch/xarclass/indexer.php");
class comments_indexer extends indexer
{
    var $name = 'comments';
    
    /**
      * Set up a base dataset from xarbb by querying what is in the fora
      *
      *
    **/
    function get_items()
    {
        $comments = xarDBGetSiteTablePrefix() . '_comments';

        // Let's skip xarbb comment thingies, since we have a separate indexer for those.
        $sql = "SELECT xar_cid as id,
                       xar_title as title,
                       xar_text as post
                FROM   $comments
                WHERE xar_pid!=? AND xar_modid!=?";
        $this->record_set = $this->db_conn->Execute($sql,array(0,300));
        if( !$this->record_set ) return false;
        return true;
    }

    /**
      * Given one item of the recordset as produced by get_items, create
      * a search index document bases on the fields given
      *
     **/
    function &make_document($fields)
    {
        list($id, $subject, $comment) = $fields;

        // The url for the comment is depending on the item for which the comment applies
        // For now, we do this by hand
        $module='comments';$type='user';$func='display';
        $params = array('cid' => $id);
        $url = xarModURL($module,$type,$func,$params);


        $document = new XapianDocument();

        // Index the subject and the text, where subject is a bit more important to match
        $this->index_text($subject, $document, $weight=3);
        $this->index_text($comment, $document, $weight=2);

        /*
            Add values to documents for use in displaying search results
        */
        $i = 0;
        $document->add_value($i++, $id);
        $document->add_value($i++, $subject);
        // For the excerpt in results, 255 is enough
        // @todo, make this a modvar for sitesearch
        $document->add_value($i++, substr(strip_tags(trim($comment)), 0, 255));
        $document->add_value($i++, 'HTML');
        $document->add_value($i, $url);

        $this->database->replace_document((int) $id, $document);

        return $document;
    }
}


?>