<?php

/**
 * make a digest of new articles (executed by the scheduler module)
 * e.g. for sending out to users, for use by the pubsub module, ...
 * 
 * @author mikespub
 * @access public 
 */
function articles_schedulerapi_makedigest($args)
{

// TODO: get some configuration info about which pubtypes, categories, statuses, ... are
//       concerned, if there are any limits to the number of articles to put in the digest,
//       etc. Then retrieve the relevant articles, create the digest and do whatever :-)

    return true;
}

?>
