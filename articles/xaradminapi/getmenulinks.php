<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function articles_adminapi_getmenulinks()
{
    $menulinks = array();

// Security Check
    if (xarSecurityCheck('EditArticles',0)) {

        $menulinks[] = Array('url'   => xarModURL('articles',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and edit all articles'),
                              'label' => xarML('View Articles'));
    }

// Security Check
    if (xarSecurityCheck('SubmitArticles',0)) {

        $menulinks[] = Array('url'   => xarModURL('articles',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Add a new article'),
                              'label' => xarML('Add Article'));
    }


// Security Check
    if (xarSecurityCheck('AdminArticles',0)) {

        $menulinks[] = Array('url'   => xarModURL('articles',
                                                   'admin',
                                                   'importpages'),
                              'title' => xarML('Import existing HTML pages'),
                              'label' => xarML('Import Webpages'));

        $menulinks[] = Array('url'   => xarModURL('articles',
                                                   'admin',
                                                   'pubtypes'),
                              'title' => xarML('View and edit publication types'),
                              'label' => xarML('Publication Types'));

    // TODO: differentiate security check according to pubtype ?
        $menulinks[] = Array('url'   => xarModURL('articles',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the articles module configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>
