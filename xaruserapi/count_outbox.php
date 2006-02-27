<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_userapi_count_outbox()
{
    // Load the comments api so we can have the defines
    xarModAPILoad('comments','user');

    $total = xarModAPIFunc('comments',
                            'user',
                            'get_author_count',
                             array('modid'  => xarModGetIDFromName('messages'),
                                   'author' => xarUserGetVar('uid'),
                                   'status' => _COM_STATUS_OFF
                                  )
                            );

    return $total;
}

?>