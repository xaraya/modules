<?php
/**
 * Featured items
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 *
 */
/**
 * Featured initialise block
 *
 * @author Jonn Beams (based on code from TopItems block)
 *
 */
    sys::import('modules.publications.xarblocks.topitems');

    class FeatureditemsBlock extends TopitemsBlock
    {
        public $featuredid          = 0;
        public $alttitle            = '';
        public $altsummary          = '';
        public $moreitems           = array();
        public $showfeaturedsum     = false;
        public $showfeaturedbod     = false;
        public $numitems            = 5;

        public function __construct(ObjectDescriptor $descriptor)
        {
            parent::__construct($descriptor);
            $this->text_type = 'Featured Items';
            $this->text_type_long = 'Show featured publications';
            $this->allow_multiple = true;
            $this->show_preview = true;

            $this->toptype = 'ratings';
        }

        public function display(Array $data=array())
        {
            $data = parent::display($data);

    // Defaults
    if (empty($vars['featuredid'])) {$vars['featuredid'] = 0;}
    if (empty($vars['alttitle'])) {$vars['alttitle'] = '';}
    if (empty($vars['altsummary'])) {$vars['altsummary'] = '';}
    if (empty($vars['toptype'])) {$vars['toptype'] = 'date';}
    if (empty($vars['moreitems'])) {$vars['moreitems'] = array();}
    if (empty($vars['linkcat'])) {$vars['linkcat'] = false;}
    if (!isset($vars['showvalue'])) {
        if ($vars['toptype'] == 'rating') {
            $vars['showvalue'] = false;
        } else {
            $vars['showvalue'] = true;
        }
    }

    $featuredid = $vars['featuredid'];

    $fields = array('id', 'title', 'cids');

    $fields[] = 'dynamicdata';

    // Initialize arrays
    $data['feature'] = array();
    $data['items'] = array();

    // Setup featured item
    if ($featuredid > 0) {

        if (xarModIsHooked('uploads', 'publications', $vars['pubtype_id'])) {
            xarVarSetCached('Hooks.uploads','ishooked',1);
        }

          if($featart = xarModAPIFunc(
            'publications','user','getall',
            array(
                'ids' => array($featuredid),
                'extra' => array('cids','dynamicdata')))) {

                foreach($featart as $featuredart) {

            $fieldlist = array('id', 'title', 'summary', 'owner', 'pubdate',
                               'pubtype_id', 'notes', 'state', 'body', 'cids');

            $featuredlink = xarModURL(
                'publications', 'user', 'display',
                array(
                    'id' => $featuredart['id'],
                    'itemtype' => (!empty($vars['linkpubtype']) ? $featuredart['pubtype_id'] : NULL),
                    'catid' => ((!empty($vars['linkcat']) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                )
            );
            if (empty($vars['showfeaturedbod'])) {$vars['showfeaturedbod'] = false;}
            if(!isset($featuredart['cids'])) $featuredart['cids'] = "";

            $feature= array(
                'featuredlabel'     => $featuredart['title'],
                'featuredlink'      => $featuredlink,
                'alttitle'          => $vars['alttitle'],
                'altsummary'        => $vars['altsummary'],
                'showfeaturedsum'   => $vars['showfeaturedsum'],
                'showfeaturedbod'   => $vars['showfeaturedbod'],
                'featureddesc'      => $featuredart['summary'],
                'featuredbody'      => $featuredart['body'],
                'featuredcids'      => $featuredart['cids'],
                'pubtype_id'         => $featuredart['pubtype_id'],
                'featuredid'       => $featuredart['id'],
                'featureddate'      => $featuredart['pubdate']
            );

            // Get rid of the default fields so all we have left are the DD ones
            foreach ($fieldlist as $field) {
                if (isset($featuredart[$field])) {
                    unset($featuredart[$field]);
                }
            }

            // now add the DD fields to the featuredart
            $feature = array_merge($featuredart, $feature);
            $data['feature'][] = $feature;
        }
    }

    // Setup additional items
    $fields = array('id', 'title', 'pubtype_id', 'cids');

    // Added the 'summary' field to the field list.
    if (!empty($vars['showsummary'])) {
        $fields[] = 'summary';
    }

    if ($vars['toptype'] == 'rating') {
        $fields[] = 'rating';
        $sort = 'rating';
    } elseif ($vars['toptype'] == 'hits') {
        $fields[] = 'counter';
        $sort = 'hits';
    } elseif ($vars['toptype'] == 'date') {
        $fields[] = 'pubdate';
        $sort = 'date';
    } else {
       $sort = $vars['toptype'];
    }

    if (!empty($vars['moreitems'])) {
        $publications = xarModAPIFunc(
            'publications', 'user', 'getall',
            array(
                'ids' => $vars['moreitems'],
                'enddate' => time(),
                'fields' => $fields,
                'sort' => $sort
            )
        );

        // See if we're currently displaying an article
        if (xarVarIsCached('Blocks.publications', 'id')) {
            $curid = xarVarGetCached('Blocks.publications', 'id');
        } else {
            $curid = -1;
        }

        foreach ($publications as $article) {
            if ($article['id'] != $curid) {
                $link = xarModURL(
                    'publications', 'user', 'display',
                    array (
                        'id' => $article['id'],
                        'itemtype' => (!empty($vars['linkpubtype']) ? $article['pubtype_id'] : NULL),
                        'catid' => ((!empty($vars['linkcat']) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                    )
                );
            } else {
                $link = '';
            }

            $count = '';
            // TODO: find a nice clean way to show all sort types
            if ($vars['showvalue']) {
                if ($vars['toptype'] == 'rating') {
                    $count = intval($article['rating']);
                } elseif ($vars['toptype'] == 'hits') {
                    $count = $article['counter'];
                } elseif ($vars['toptype'] == 'date') {
                    // TODO: make user-dependent
                    if (!empty($article['pubdate'])) {
                        $count = strftime("%Y-%m-%d", $article['pubdate']);
                    } else {
                        $count = 0;
                    }
                } else {
                    $count = 0;
                }
            } else {
                $count = 0;
            }
            if (isset($article['cids'])) {
               $cids=$article['cids'];
            }else{
               $cids='';
            }
            if (isset($article['pubdate'])) {
               $pubdate=$article['pubdate'];
            }else{
               $pubdate='';
            }
            // Pass $desc to items[] array so that the block template can render it
            $data['items'][] = array(
                'label' => $article['title'],
                'link' => $link,
                'count' => $count,
                'cids' => $cids,
                'pubdate' => $pubdate,
                'desc' => ((!empty($vars['showsummary']) && !empty($article['summary'])) ? $article['summary'] : ''),
                'id' => $article['id']
            );
        }
    }}
    if (empty($data['feature']) && empty($data['items'])) {
        // Nothing to display.
        return;
    }

    // Set the data to return.
    $blockinfo['content'] = $data;
    return $blockinfo;
}

        public function modify(Array $data=array())
        {
            $data = parent::modify($data);

            // Defaults
            if (empty($data['featuredid'])) {$data['featuredid'] = $this->featuredid;}
            if (empty($data['alttitle'])) {$data['alttitle'] = $this->alttitle;}
            if (empty($data['altsummary'])) {$data['altsummary'] = $this->altsummary;}
            if (empty($data['showfeaturedsum'])) {$data['showfeaturedsum'] = $this->showfeaturedsum;}
            if (empty($data['showfeaturedbod'])) {$data['showfeaturedbod'] = $this->showfeaturedbod;}
            if (empty($data['moreitems'])) {$data['moreitems'] = $this->moreitems;}

            $data['fields'] = array('id', 'title');

            if (!is_array($data['state'])) $statearray = array($data['state']);
            else $statearray = $data['state'];

            if(!empty($data['catfilter'])) $cidsarray = array($data['catfilter']);
            else $cidsarray = array();

            // Create array based on modifications
            $article_args = array();

            // Only include pubtype if a specific pubtype is selected
            if (!empty($data['pubtype_id'])) $article_args['ptid'] = $data['pubtype_id'];

            // Add the rest of the arguments
            $article_args['cids'] = $cidsarray;
            $article_args['enddate'] = time();
            $article_args['state'] = $statearray;
            $article_args['fields'] = $data['fields'];
            $article_args['sort'] = $data['toptype'];

            $data['filtereditems'] = xarModAPIFunc(
                'publications', 'user', 'getall', $article_args );

            // Try to keep the additional headlines select list width less than 50 characters
            for ($idx = 0; $idx < count($data['filtereditems']); $idx++) {
                if (strlen($data['filtereditems'][$idx]['title']) > 50) {
                    $data['filtereditems'][$idx]['title'] = substr($data['filtereditems'][$idx]['title'], 0, 47) . '...';
                }
                $data['filtereditems'][$idx]['name'] = $data['filtereditems'][$idx]['title'];
            }

            //Put together the additional featured publications list
            for($idx=0; $idx < count($data['filtereditems']); ++$idx) {
                $data['filtereditems'][$idx]['selected'] = '';
                for($mx=0; $mx < count($data['moreitems']); ++$mx) {
                    if (($data['moreitems'][$mx]) == ($data['filtereditems'][$idx]['id'])) {
                        $data['filtereditems'][$idx]['selected'] = 'selected';
                    }
                }
            }
            $data['morepublications'] = $data['filtereditems'];

            return $data;
        }

        public function update(Array $data=array())
        {
            xarVarFetch('featuredid', 'id', $vars['featuredid'], 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('alttitle', 'str', $vars['alttitle'], '', XARVAR_NOT_REQUIRED);
            xarVarFetch('altsummary', 'str', $vars['altsummary'], '', XARVAR_NOT_REQUIRED);
            xarVarFetch('moreitems', 'list:id', $vars['moreitems'], NULL, XARVAR_NOT_REQUIRED);
            xarVarFetch('showfeaturedbod', 'checkbox', $vars['showfeaturedbod'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showfeaturedsum', 'checkbox', $vars['showfeaturedsum'], false, XARVAR_NOT_REQUIRED);

            return parent::update($data);
        }
    }

?>