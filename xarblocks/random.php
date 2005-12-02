<?php
/**
 * File: $Id: random.php,v 1.1.1.1 2005/11/28 18:55:21 curtis Exp $
 * 
 * Random Verse block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */

/**
 * initialise block
 */
function bible_randomblock_init()
{
    return array('rotation' => 'pageload',
                 'lastchange' => strtotime('1 years ago'),
                 'queries' => '|Proverbs',
                 'lastquery' => '|Proverbs 1:1');
}

/**
 * get information on block
 */
function bible_randomblock_info()
{
    // Values
    return array('text_type' => 'Random Verse',
        'module' => 'bible',
        'text_type_long' => 'Show random Bible verse',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 */
function bible_randomblock_display($blockinfo)
{ 
    // Security check
    if (!xarSecurityCheck('ReadBibleBlock', 0, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block.
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['rotation'])) $vars['rotation'] = 'pageload';
    if (empty($vars['lastchange'])) $vars['lastchange'] = strtotime('1 month ago');
    if (empty($vars['lastquery'])) $vars['lastquery'] = '|Proverbs 1:1';
    if (empty($vars['queries'])) $vars['queries'] = '|Proverbs';

    // initialize data array
    $data = array();
    $data['status'] = '';

    // pick a new verse if needed
    $change = false;
    if (($vars['rotation'] == 'monthly'  && $vars['lastchange'] < strtotime('1 months ago')) ||
        ($vars['rotation'] == 'biweekly' && $vars['lastchange'] < strtotime('2 weeks ago')) ||
        ($vars['rotation'] == 'weekly'   && $vars['lastchange'] < strtotime('1 weeks ago')) ||
        ($vars['rotation'] == 'daily'    && $vars['lastchange'] < strtotime('1 days ago')) ||
        ($vars['rotation'] == 'hourly'   && $vars['lastchange'] < strtotime('1 hours ago')) ||
        ($vars['rotation'] == 'pageload')) {

        $change = true;

		// get rid of extra whitespace
		$querylist = trim($vars['queries']);
		$querylist = preg_replace("/\s*,?\s*(\r\n|\n\r|\n|\r)+\s*/", "\n", $querylist);
		// split list into an array
		$querylist = preg_split("/\n/", $querylist);
		// pick a random entry from the list
		$querykey = rand(0, count($querylist)-1);
		$queryline = $querylist[$querykey];
		// split it apart to get our sname, query, and any comments
        $parts = preg_split("/\s*\|\s*/", $queryline);
        if (isset($parts[0])) {
            $sname = $parts[0];
            if (isset($parts[1])) {
                $query = $parts[1];
                if (isset($parts[2])) $comment = $parts[2];
            }
        }

		// make sure query isn't empty
		if (empty($query)) {
            $data['status'] = "Error, query for line ".($querykey+1)." is blank.";
            $data['blockid'] = $blockinfo['bid'];
            $blockinfo['content'] = $data;
            return $blockinfo;
		}

    } else {

        // get the same result as last time
        $parts = explode('|', $vars['lastquery']);
        $sname = $parts[0];
        $query = $parts[1];
        if (isset($parts[2])) $comment = $parts[2];

    }

    // if no sname given, pick the first text available
    if (empty($sname)) {

        // get all active texts
        $texts = xarModAPIFunc('bible', 'user', 'getall',
				array('state' => 2, 'type' => 1, 'numitems' => 1));

        // check for errors on texts
        if (empty($texts)) {
            $data['status'] = xarML('Error, no texts are active.');
            $data['blockid'] = $blockinfo['bid'];
            $blockinfo['content'] = $data;
            return $blockinfo;
        }

		// get sname
        $sname = $texts[key($texts)]['sname'];

	// if sname is given, make sure that text is available
    } else {

        // get all active texts
        $text = xarModAPIFunc('bible', 'user', 'get', array('sname' => $sname));

        // check for errors on texts
        if (empty($text) || $text['state'] != 2) {
            $data['status'] = "Error, $sname text not available.";
            $data['blockid'] = $blockinfo['bid'];
            $blockinfo['content'] = $data;
            return $blockinfo;
        }
	}

    // get query type
    $queryfunction = xarModAPIFunc('bible', 'user', 'getquerytype',
          		                    array('sname' => $sname, 'query' => $query));
	$queryfunction = strtr($queryfunction, array('display' => 'lookup', 'view' => 'search'));
	// display==(passage)lookup; view==(keyword)search

    // perform the query, selecting one random hit from the results
    $verse = $count = 0;
    $args = array('sname' => $sname, 'query' => $query);
    if (!empty($querylist) && count($querylist) == 1) {
        $args['numitems'] = 1;
        $args['rand'] = true;
    }
	if ($queryfunction == 'lookup') $args['nozero'] = true;
    $results = xarModAPIFunc('bible', 'user', $queryfunction, $args);

	// if empty result set, return
	if (empty($results)) {
	    $data['status'] = xarML('Error, no query results.');
        $data['blockid'] = $blockinfo['bid'];
        $blockinfo['content'] = $data;
        return $blockinfo;
	}

    // format the results
    $results = xarModAPIFunc('bible', 'user', 'formattext',
                             array('data' => $results,
                                   'strongs' => false,
								   'search' => $queryfunction,
								   'lookup' => ($queryfunction == 'lookup') ? true : false));

    // add some things to the results, and pass results to template
    $results['comment'] = empty($comment) ? '' : $comment;
    $results['ref'] = (count($results['lines']) == 1) ? $results['lines'][0]['ref'] : $results['ref'];
    $data['passage'] = $results;

    // save updated params if we changed them (don't do it if rotation is pageload)
    if ($change && $vars['rotation'] != 'pageload') {
        $blockinfo['content'] = array('rotation' => $vars['rotation'],
                                      'queries' => $vars['queries'],
                                      'lastchange' => time(),
                                      'lastquery' => "$sname|$results[ref]|$results[comment]");

		// This does part of what xarModAPIFunc('blocks', 'admin', 'update_instance') does, but
		// we can't use that function because a normal user doesn't have permissions for it.
	    $dbconn = xarDBGetConn();
	    $xartable = xarDBGetTables();
	    $block_instances_table = $xartable['block_instances'];
	    $block_group_instances_table = $xartable['block_group_instances'];

	    $query = "UPDATE $block_instances_table SET xar_content = ? WHERE xar_id = ?";
	    $bindvars = array(serialize($blockinfo['content']), $blockinfo['bid']);
	    $result = $dbconn->Execute($query, $bindvars);
	    if (!$result) return;
    }

    $data['blockid'] = $blockinfo['bid'];
    $blockinfo['content'] = $data;
    return $blockinfo;
} 

function bible_randomblock_saveparams($blockinfo)
{
    extract($blockinfo);



	return true;

}

?>
