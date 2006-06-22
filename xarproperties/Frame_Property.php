<?php
/**
 * Frame Property
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Window module
 * @link http://xaraya.com/index.php/release/68.html
 */
/**
 * @author Marc Lutolf <mfl@netspan.ch>
*/
include_once "modules/dynamicdata/class/properties.php";

class Frame_Property extends Dynamic_Property
{
    public $hsize;
    public $vsize;
    public $url = "index.php?module=window";
    public $title = "Xaraya Window";
    public $auto_resize;
    public $allow_local_only;
    public $use_buffering;
    public $reg_user_only;
    public $no_user_entry;
    public $open_direct;
    public $use_fixed_title;
    public $security;
    public $use_iframe;
    public $use_object;

    function __construct($args)
    {
        parent::__construct($args);

        $this->tplmodule = 'window';
        $this->template = 'frame';
		$this->filepath   = 'modules/window/xarproperties';

        $this->hsize = xarModGetVar('window', 'hsize');
        $this->vsize = xarModGetVar('window', 'vsize');
        $this->auto_resize = xarModGetVar('window', 'auto_resize');
        $this->allow_local_only = xarModGetVar('window', 'allow_local_only');
        $this->use_buffering = xarModGetVar('window', 'use_buffering');
        $this->reg_user_only = xarModGetVar('window', 'reg_user_only');
        $this->no_user_entry = xarModGetVar('window', 'no_user_entry');
        $this->open_direct = xarModGetVar('window', 'open_direct');
        $this->use_fixed_title = xarModGetVar('window', 'use_fixed_title');
        $this->security = xarModGetVar('window', 'security');
        $this->use_iframe = xarModGetVar('window', 'use_iframe');
        $this->use_object = xarModGetVar('window', 'use_object');

        if(isset($args['hsize'])) $this->hsize = $args['hsize'];
        if(isset($args['vsize'])) $this->vsize = $args['vsize'];
        if(isset($args['auto_resize'])) $this->auto_resize = $args['auto_resize'];

        // check validation for allowed rows/cols (or values)
        if (!empty($this->validation)) {
            $this->parseValidation($this->validation);
        }
    }

    static function getRegistrationInfo()
    {
        $info = new PropertyRegistration();
        $info->reqmodules = array('window');
        $info->id      = 30037;
        $info->name    = 'frame';
        $info->desc    = 'Window Frame';
        return $info;
    }

    function showInput($data = array())
    {
        extract($data);

        // Prepare
        $data['value'] = isset($value) ? xarVarPrepForDisplay($value) : xarVarPrepForDisplay($this->value);
        // TODO: the way the template is organized now, this only works when an id is set.
        $hsize  = isset($hsize) ? $hsize : $this->hsize;
        $vsize  = isset($vsize) ? $vsize : $this->vsize;
		$url           = isset($url) ? $url : $this->url;
		$auto_resize    = isset($auto_resize) ? $auto_resize : $this->auto_resize;
		$open_direct    = isset($open_direct) ? $open_direct : $this->open_direct;
		$use_iframe     = isset($use_iframe) ? $use_iframe : $this->use_iframe;
		$use_object     = isset($use_object) ? $use_object : $this->use_object;
		$use_fixed_title     = isset($use_fixed_title) ? $use_fixed_title : $this->use_fixed_title;

		if ($this->security) {
			if (!xarSecurityCheck('ReadWindow')) return;

			$pageinfo = xarModAPIFunc('window','user','get',array('name' => $url));

			if ($pageinfo) {
				$url = $pageinfo['name'];
				$reg_user_only = $pageinfo['reg_user_only'];
				$open_direct = $pageinfo['open_direct'];
				$use_fixed_title = $pageinfo['use_fixed_title'];
				$auto_resize = $pageinfo['auto_resize'];
				$vsize = $pageinfo['vsize'];
				$hsize = $pageinfo['hsize'];
				$title = $pageinfo['label'];

			} else {
				// Look harder
				$dbconn =& xarDBGetConn();
				$xartable =& xarDBGetTables();
				$urltable = $xartable['window'];

				$result = $dbconn->Execute("SELECT * FROM $urltable");
				if(!$result) return;

				$db_checked = 0;

				if(!$result->EOF) {
					while(list($id, $name, $alias, $label, $description, $reg_user_only1, $open_direct1, $use_fixed_title1, $auto_resize1, $vsize1, $hsize1) = $result->fields) {

						// Check if URL is in DB
						if (($alias == $url) || ($name == $url) || ($name == "http://".$url)) {
							$db_checked = 1;
							$url = $url;
							// Override global settings
							$reg_user_only = $reg_user_only1;
							$open_direct = $open_direct1;
							$use_fixed_title = $use_fixed_title1;
							$auto_resize = $auto_resize1;
							$vsize = $vsize1;
							$hsize = $hsize1;
							$title = $label;
							break;
						}
						$result->MoveNext();
					}
				}
			}
		}

		// Store URL parts in array
		$url_parts = parse_url($url);


		// Check that a url was specified
		if(!isset($url) || ($url == '')) {
			$msg = xarML('No page to display was specified.',
				'window');
			xarErrorSet(XAR_USER_EXCEPTION,
				'NOT_ALLOWED',
				new DefaultUserException($msg));
			return;
		}

		// Check for not entered in browser location window if set
		if (!$_SERVER['REMOTE_ADDR'] && !$no_user_entry) {
			$msg = xarML('You cannot access this page via a link.',
				'window');
			xarErrorSet(XAR_USER_EXCEPTION,
				'NOT_ALLOWED',
				new DefaultUserException($msg));
			return;
		}

		// Check for not local page if set
		if($this->allow_local_only &&
			(isset($url_parts['host'])) &&
			($url_parts['host'] != xarServerGetHost())) {
			$msg = xarML('Only pages off your local server can be displayed.', 'window');
			xarErrorSet(XAR_USER_EXCEPTION, 'NOT_ALLOWED', new DefaultUserException($msg));
			return;
		}

		// Check that user is registered and logged in if set
		if(!xarUserIsLoggedIn() && ($reg_user_only)) {
			$msg = xarML('Only registered users can view this page.',
				'window');
			xarErrorSet(XAR_USER_EXCEPTION,
				'NOT_ALLOWED',
				new DefaultUserException($msg));
			return;
		}

		// Everything is good - ready to display

		// Check for fixed title and use it
		// Check if title was passed in URL
		if(!isset($title)) {
			if($use_fixed_title) {
				$title = $this->title;
			}
			else {
				$title = '';
			}
		}
		else {
			$end_title = '';
		}

		// Check if height, width or resize were passed in URL
		if (isset($height)) {
			$vsize = $height;
			$this->auto_resize = false;
		}

		if(isset($width)) {
			$this->hsize = $width;
		} elseif (!$hsize) {
			$this->hsize = '100%';
		}

		if (isset($resize) && $resize == 1) {
			$this->auto_resize = true;
		}

		if (isset($id)) {
			$data['hooks'] = xarModCallHooks('item', 'display', $id, array('itemtype'  => $id,
																		   'returnurl' => xarModURL('window', 'user', 'main', array('page' => $url, 'id' => $id))),
																	'window');
		}

		$data['url'] = $url;
		$data['title'] = $title;
		$data['hsize'] = $hsize;
		$data['vsize'] = $vsize;
		$data['auto_resize'] = $auto_resize;
		$data['open_direct'] = $open_direct;
		$data['use_iframe'] = $use_iframe;
		$data['use_object'] = $use_object;

        // Let parent deal with the rest
        return parent::showInput($data);
    }

    // check validation for allowed rows/cols (or values)
    function parseValidation($validation = '')
    {
        if (is_string($validation) && strchr($validation,':')) {
            list($rows,$cols) = explode(':',$validation);
            if ($rows !== '' && is_numeric($rows)) {
                $this->rows = $rows;
            }
            if ($cols !== '' && is_numeric($cols)) {
                $this->cols = $cols;
            }
        }
    }
}

?>
