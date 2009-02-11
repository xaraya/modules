<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * change the state of publications based on some criteria (executed by the scheduler module)
 * e.g. to expire publications from the frontpage or whatever
 *
 * @author mikespub
 * @access public
 */
function publications_schedulerapi_changestate($args)
{

// TODO: get some configuration info about which pubtypes, categories, statees, ... are
//       concerned, if there is any minimum number of publications to leave in a certain state,
//       etc. Then retrieve the relevant publications and change their state accordingly :-)

// Note: for more advanced/customised state handling, you should define a workflow

/*
Here you could e.g. update the state of all publications of a certain
publication type, that have the frontpage state, and that have
been published more than x time ago. The fastest way is to
do that directly via SQL :
*/

/*
    $dbconn = xarDB::getConn();
    $xartables = xarDB::getTables();

    // publications of publication type 1 (= news or whatever)
    $pubtype_id = 1;
    // that were published at least 7 days ago
    $pubdate = time() - 7 * 24 * 60 * 60;
    // and still have the state 3 (= frontpage)
    $oldstate = 3;
    // will receive the new state 2 (= approved)
    $newstate = 2;

    $query = 'UPDATE ' . $xartables['publications'] . '
                SET state = ' . $newstate . '
              WHERE pubtype_id = ' . $pubtype_id . '
                AND pubdate < ' . $pubdate . '
                AND state = ' . $oldstate;

    $result =& $dbconn->Execute($query);
    if (!$result) return;
*/

/*
If you put this in changestate.php and schedule that every day (or whatever)
those publications will "expire" automatically.

[Note : instead of the SQL, you can also use the getall() function to retrieve
the publications you want, and the update() function shown above to update
them individually.]

Some extension might be to "expire" only publications that were not published
by a certain author (e.g. yourself), or that are (not) in a certain category,
or whatever, but you get the idea :-)
*/

    return true;
}

?>
