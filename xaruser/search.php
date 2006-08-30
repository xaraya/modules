<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    Search Tickets

    Search form for searching the Tickets

    @author  Brian McGilligan bmcgilligan@abrasiontechnology.com
    @access  public / private / protected
    @param
    @param
    @return  template
    @throws  list of exception identifiers which can be thrown
    @todo    <Brian McGilligan> ;
*/
function helpdesk_user_search()
{
    if( !Security::check(SECURITY_READ, 'helpdesk', TICKET_ITEMTYPE) ){ return true; }

    $data['enabledimages'] = xarModGetVar('helpdesk', 'Enable Images');
    $data['username']      = xarUserGetVar('uname');
    $data['userid']        = xarUserGetVar('uid');

    return xarTplModule('helpdesk', 'user', 'search', $data);
}
?>