<?php
/**
 * Display the terms in a volume
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @link http://xaraya.com/index.php/release/221.html
 * @author Marc Lutolf
 */

include_once 'modules/encyclopedia/xarclasses/encyclopediaquery.php';

function encyclopedia_user_displayvol()
{
    if(!xarVarFetch('vid',   'int', $vid   , 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('vvid',    'int', $vvid,      0,     XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('letter', 'str:1:2', $letter, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('search', 'str:1:100', $search, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('searchtype',    'str', $searchtype,      'term',     XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('startnum', 'int', $startnum,   1,      XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('order',    'str', $order,      NULL,     XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('phase',    'int', $data['phase'],      0,     XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('confirmed',    'int', $data['confirmed'],      1,     XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('ReadEncyclopedia')) {return;}

    // Retrieve a current selection of the data if there is one; else create one
    $cs = xarSessionGetVar('currentselection');
    if (empty($cs)) {
        // There is no current query. Create one and define some standard stuff.
        $cs = new EncyclopediaQuery();
        $cs->eq('active',1);
        $cs->eq('validated',1);
        $cs->getrowstodo(xarModGetVar('encyclopedia', 'itemsperpage'));
    } else {
        $cs = unserialize($cs);
        //FIXME: remove this line once the security scenario is merged (becomes unnecessary)
        $cs->openconnection();
    }

    // Filter on the volume we are interested in
    $cs->eq('vid',$vid);

    // Set the record to start displaying at
    $cs->setstartat($startnum);

    // If a column header was clicked, arrange the proper sorting order
    if (isset($order)) {
        $sort = $cs->getsort();
        if($sort == 'ASC') $sort = 'DESC';
        else $sort = 'ASC';
        $cs->setorder($order,$sort);
    }

// At this point we have a "complete" query.
// The only thing missing are the search criteria and/or letter filter.
// We'll insert those in the lettersearch API call below

// Define the letters for the alphabet we will display
    $data['alphabet'] = array(
        'A', 'B', 'C', 'D', 'E', 'F',
        'G', 'H', 'I', 'J', 'K', 'L',
        'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z'
    );
    //this was commented out - if so the templates errors with undefined $volumes var
    //uncommented for now
    $volumes = xarModAPIFunc('encyclopedia',
                          'user',
                          'vols');
    $data['volumes'] = array_merge(array(array('id' => 0, 'name' => xarML("All Volumes"))), $volumes);

    $data['total'] = 0;
    $data['items'] = array();
    $data['msg'] = '';

    if ($data['confirmed']) {

        // Which volume(s) are we searching in?
        if( $vvid != "allvols" ) $cs->eq('vid',$vvid);

        // This API call adds the letter filter and/or search phrase filter
        // executes the query and returns the results
        $items = xarModAPIFunc('encyclopedia', 'user', 'lettersearch',
                             array('cs' => &$cs,
                                   'letterget' => $letter,
                                   'search' => $search,
                                   'searchtype' => $searchtype));
        $numberItems = count($items);
        $configSaysColumns = xarModGetVar('Encyclopedia', 'columns');
        $numberOfColumnsToUse = $configSaysColumns; // situation can override config setting
        $maxRows = (int) ceil($numberItems  / $configSaysColumns);

        if( $numberItems < ($configSaysColumns * 2) )
        {
            // You need at least two per column to look good, so if you don't just have one column
            $numberOfColumnsToUse = 1;
            $maxRows = $numberItems;
        }
        $data['$numberOfColumnsToUse'] = $numberOfColumnsToUse;
        $data['$maxRows'] = $maxRows;
        $data['items'] = $items;
    }

    // Now that we have the final query, save it as the current query
    xarSessionSetVar('currentselection', serialize($cs));

    $data['pager'] = xarTplGetPager($startnum,
                            $data['total'],
                            xarModURL('encyclopedia', 'user', 'displayvol',array('startnum' => '%%')),
                            xarModGetVar('encyclopedia', 'itemsperpage'));
    $data['order'] = $order;
    $data['letter'] = $letter;
    $data['vid'] = $vid;
    $data['search'] = $search;
    return $data;
}

?>