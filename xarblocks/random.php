<?php
/**
 * Random Block
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
 * initialise block
 * @author Roger Keays
 */
    sys::import('modules.publications.xarblocks.topitems');

    class RandomBlock extends TopitemsBlock
    {
        public $locale       = '';
        public $alttitle     = '';
        public $altsummary   = '';
        public $showtitle    = true;
        public $showsummary  = true;
        public $showpubdate  = false;
        public $showauthor   = false;
        public $showsubmit   = false;

        public function __construct(ObjectDescriptor $descriptor)
        {
            parent::__construct($descriptor);
            $this->text_type = 'Random publication';
            $this->text_type_long = 'Show a single random publication';
        }

        public function display(Array $data=array())
        {
            $data = parent::display($data);

            // frontpage or approved state
            if (empty($data['state'])) {
                    $statearray = array(2,3);
            } elseif (!is_array($data['state'])) {
                    $statearray = split(',', $data['state']);
            } else {
                    $statearray = $data['state'];
            }

            if (empty($data['locale'])) {
                $lang = null;
            } elseif ($data['locale'] == 'current') {
                $lang = xarMLSGetCurrentLocale();
            } else {
                $lang = $data['locale'];
            }

            // get cids for security check in getall
            $fields = array('id', 'title', 'body', 'notes', 'pubtype_id', 'cids', 'owner');

            if (!empty($data['showpubdate'])) array_push($fields, 'pubdate');
            if (!empty($data['showsummary'])) array_push($fields, 'summary');
            if (!empty($data['showauthor'])) array_push($fields, 'owner');
            if (!empty($data['alttitle'])) $blockinfo['title'] = $data['alttitle'];
            if (empty($data['pubtype_id'])) $data['pubtype_id'] = 0;

            if (!empty($data['catfilter'])) {
                // use admin defined category
                $cidsarray = array($data['catfilter']);
                $cid = $data['catfilter'];
            } else {
                $cid = 0;
                $cidsarray = array();
            }

            // check if dynamicdata is hooked for all pubtypes or the current one (= defaults to 0 anyway here)
            if (!empty($data['showdynamic']) && xarModIsHooked('dynamicdata', 'publications', $data['pubtype_id'])) {
                array_push($fields, 'dynamicdata');
            }

            if (empty($data['numitems'])) $data['numitems'] = 1;

            $publications = xarModAPIFunc('publications','user','getrandom',
                                      array('ptid'     => $data['pubtype_id'],
                                            'cids'     => $cidsarray,
                                            'andcids'  => false,
                                            'state'   => $statearray,
                                            'locale' => $lang,
                                            'numitems' => $data['numitems'],
                                            'fields'   => $fields,
                                            'unique'   => true));

            if (!isset($publications) || !is_array($publications) || count($publications) == 0) {
                return;
            } else {
                foreach (array_keys($publications) as $key) {
                    // for template compatibility :-(
                    if (!empty($publications[$key]['author']) && !empty($data['showauthor'])) {
                        $publications[$key]['authorname'] = $publications[$key]['author'];
                    }
                    $data['items'][] = $publications[$key];
                }
            }

            return;$data;
        }

        public function modify(Array $data=array())
        {
            $data = parent::modify($data);
            if (empty($data['locale'])) {$data['locale'] = $this->locale;}
            if (empty($data['alttitle'])) {$data['alttitle'] = $this->alttitle;}
            if (empty($data['altsummary'])) {$data['altsummary'] = $this->altsummary;}
            if (empty($data['showtitle'])) {$data['showtitle'] = $this->showtitle;}
            if (empty($data['showsummary'])) {$data['showsummary'] = $this->showsummary;}
            if (empty($data['showpubdate'])) {$data['showpubdate'] = $this->showpubdate;}
            if (empty($data['showauthor'])) {$data['showauthor'] = $this->showauthor;}
            if (empty($data['showsubmit'])) {$data['showsubmit'] = $this->showsubmit;}
            if(!empty($data['catfilter'])) {
                $cidsarray = array($data['catfilter']);
            } else {
                $cidsarray = array();
            }

            $data['locales'] = xarMLSListSiteLocales();
            asort($data['locales']);

            return $data;
        }

        public function update(Array $data=array())
        {
            xarVarFetch('locale', 'str', $data['locale'], '', XARVAR_NOT_REQUIRED);
            xarVarFetch('alttitle', 'str', $data['alttitle'], '', XARVAR_NOT_REQUIRED);
            xarVarFetch('altsummary', 'str', $data['altsummary'], '', XARVAR_NOT_REQUIRED);
            xarVarFetch('showtitle', 'checkbox', $data['showtitle'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showsummary', 'checkbox', $data['showsummary'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showpubdate', 'checkbox', $data['showpubdate'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showauthor', 'checkbox', $data['showauthor'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showsubmit', 'checkbox', $data['showsubmit'], false, XARVAR_NOT_REQUIRED);

            return parent::update($data);
        }
    }
?>