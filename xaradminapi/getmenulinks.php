<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * utility function pass individual menu items to the Admin menu
 *
 * @author the Articles module development team
 * @return array containing menulinks
 */
function articles_adminapi_getmenulinks()
{
    static $menulinks = array();
    if (isset($menulinks[0])) {
        return $menulinks;
    }

    if (xarSecurityCheck('EditArticles',0)) {
        $menulinks[] = Array('url' => xarModURL('articles', 'admin', 'view')
            ,'active'=> array('main', 'view', 'modify', 'delete')
            ,'title' => xarML('View and edit all articles')
            ,'label' => xarML('Manage Articles')
        );
    }

    if (xarSecurityCheck('SubmitArticles',0)) {
        $menulinks[] = Array('url' => xarModURL('articles', 'admin', 'new')
            ,'active'=> array('new')
            ,'title' => xarML('Add a new article')
            ,'label' => xarML('New Article')
        );
    }

    if (xarSecurityCheck('AdminArticles',0)) {
        $menulinks[] = Array('url' => xarModURL('articles', 'admin', 'pubtypes')
            ,'active'=> array('pubtypes', 'importpubtype', 'exportpubtype')
            ,'title' => xarML('View and edit publication types')
            ,'label' => xarML('Publication Types')
        );

        $menulinks[] = Array('url' => xarModURL('articles', 'admin', 'stats')
            ,'active'=> array('stats', 'importpictures', 'importpages')
            ,'title' => xarML('View statistics, import pictures and import pages')
            ,'label' => xarML('Utilities')
        );

        $menulinks[] = Array('url' => xarModURL('articles', 'admin', 'modifyconfig')
            ,'active'=> array('modifyconfig')
            ,'title' => xarML('Modify the articles module configuration')
            ,'label' => xarML('Modify Config')
        );

        $menulinks[] = array('url' => xarModURL('articles','admin','overview')
            ,'active' => array('overview')
            ,'title'  => xarML('Introduction on handling this module')
            ,'label'  => xarML('Overview')
        );
    }

    return $menulinks;
}

?>