<?php

/**
 * change the status of articles based on some criteria (executed by the scheduler module)
 * e.g. to expire articles from the frontpage or whatever
 * 
 * @author mikespub
 * @access public 
 */
function articles_schedulerapi_changestatus($args)
{

// TODO: get some configuration info about which pubtypes, categories, statuses, ... are
//       concerned, if there is any minimum number of articles to leave in a certain status,
//       etc. Then retrieve the relevant articles and change their status accordingly :-)

// Note: for more advanced/customised status handling, you should define a workflow

    return true;
}

?>
