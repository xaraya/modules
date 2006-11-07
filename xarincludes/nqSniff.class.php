<?php
/**
 *  PHP Client Sniffer (nqSniff)
 *
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with this library; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *  @author Roger Raymond <epsilon7@users.sourceforge.net>
 *  @version $Id: nqSniff.class.php,v 1.1 2004/09/29 13:49:41 markwest Exp $
 *  @copyright Copyright &copy; 2002-2004 Roger Raymond
 *  @package nqSniff
 *  @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 *  @filesource
 *
 *  PHP Sniffer Class
 *
 *  Used to determine the browser and other associated properies
 *  using nothing other than the HTTP_USER_AGENT value supplied by a
 *  user's web browser.
 *
 *  @package nqSniff
 *  @access public
 *  @author Roger Raymond <epsilon7@users.sourceforge.net>
 */
class nqSniff
{
    var $_version = '2.1.4';
    var $_temp_file_path        = '/tmp/'; // with trailing slash
    var $_check_cookies         = NULL;
    var $_default_language      = 'en-us';
    var $_allow_masquerading    = NULL;
    var $_php_version           = '';
    var $_browsers = array(
        'microsoft internet explorer' => 'IE',
        'msie'                        => 'IE',
        'netscape6'                   => 'NS',
        'netscape'                    => 'NS',
        'galeon'                      => 'GA',
        'phoenix'                     => 'PX',
        'mozilla firebird'            => 'FB',
        'firebird'                    => 'FB',
        'firefox'                     => 'FX',
        'chimera'                     => 'CH',
        'camino'                      => 'CA',
        'epiphany'                    => 'EP',
        'safari'                      => 'SF',
        'k-meleon'                    => 'KM',
        'mozilla'                     => 'MZ',
        'opera'                       => 'OP',
        'konqueror'                   => 'KQ',
        'icab'                        => 'IC',
        'lynx'                        => 'LX',
        'links'                       => 'LI',
        'ncsa mosaic'                 => 'MO',
        'amaya'                       => 'AM',
        'omniweb'                     => 'OW',
        'hotjava'                     => 'HJ',
        'browsex'                     => 'BX',
        'amigavoyager'                => 'AV',
        'amiga-aweb'                  => 'AW',
        'ibrowse'                     => 'IB'
    );
    var $_javascript_versions = array(
        '1.5'   =>  'NS5+,MZ,PX,FB,FX,GA,CH,CA,SF,KQ3+,KM,EP', // browsers that support JavaScript 1.5
        '1.4'   =>  '',
        '1.3'   =>  'NS4.05+,OP5+,IE5+',
        '1.2'   =>  'NS4+,IE4+',
        '1.1'   =>  'NS3+,OP,KQ',
        '1.0'   =>  'NS2+,IE3+',
        '0'     =>  'LI,LX,HJ'
    );
    var $_browser_features = array(
        'html'    =>  '',
        'images'  =>  'LI,LX',
        'frames'  =>  'LX',
        'tables'  =>  '',
        'java'    =>  'OP3,LI,LX,NS1,MO,IE1,IE2',
        'plugins' =>  'IE1,IE2,LI,LX',
        'css2'    =>  'NS5+,IE5+,MZ,PX,FB,FX,CH,CA,SF,GA,KQ3+,OP7+,KM,EP',
        'css1'    =>  'NS4+,IE4+,MZ,PX,FB,FX,CH,CA,SF,GA,KQ,OP7+,KM,EP',
        'iframes' =>  'LI,IE3+,NS5+,MZ,PX,FB,FX,CH,CA,SF,GA,KQ,OP7+,KM,EP',
        'xml'     =>  'IE5+,NS5+,MZ,PX,FB,FX,CH,CA,SF,GA,KQ,OP7+,KM,EP',
        'dom'     =>  'IE5+,NS5+,MZ,PX,FB,FX,CH,CA,SF,GA,KQ,OP7+,KM,EP',
        'hdml'    =>  '',
        'wml'     =>  ''
    );
    var $_browser_quirks = array(
        'must_cache_forms'         =>  'NS,MZ,FB,PX,FX',
        'avoid_popup_windows'      =>  'IE3,LI,LX',
        'cache_ssl_downloads'      =>  'IE',
        'break_disposition_header' =>  'IE5.5',
        'empty_file_input_value'   =>  'KQ',
        'scrollbar_in_way'         =>  'IE6'
    );
    var $_browser_info = array(
        'ua'         => '',
        'browser'    => 'Unknown',
        'version'    => 0,
        'maj_ver'    => 0,
        'min_ver'    => 0,
        'letter_ver' => '',
        'javascript' => '0.0',
        'platform'   => 'Unknown',
        'os'         => 'Unknown',
        'ip'         => 'Unknown',
        'cookies'    => 'Unknown', // remains for backwards compatability
        'ss_cookies' => 'Unknown',
        'st_cookies' => 'Unknown',
        'language'   => '',
        'long_name'  => '',
        'gecko'      => '',
        'gecko_ver'  => ''
    );
    var $_feature_set = array(
        'html'     => true,
        'images'   => true,
        'frames'   => true,
        'tables'   => true,
        'java'     => true,
        'plugins'  => true,
        'iframes'  => false,
        'css2'     => false,
        'css1'     => false,
        'xml'      => false,
        'dom'      => false,
        'wml'      => false,
        'hdml'     => false
    );
    var $_quirks = array(
        'must_cache_forms'         =>  false,
        'avoid_popup_windows'      =>  false,
        'cache_ssl_downloads'      =>  false,
        'break_disposition_header' =>  false,
        'empty_file_input_value'   =>  false,
        'scrollbar_in_way'         =>  false
    );
    var $_get_languages_ran_once = false;
    var $_browser_search_regex = '([a-z]+)([0-9]*)([0-9.]*)(up|dn|\+|\-)?';
    var $_language_search_regex = '([a-z-]{2,})';
    var $_browser_regex;
    function nqSniff($UA='',$settings = true)
    {
        if(is_array($settings)) {
            $run = true;
            extract($settings);
            $this->_check_cookies = $check_cookies;
            $this->_default_language = $default_language;
            $this->_allow_masquerading = $allow_masquerading;
        } else {
            $run = (bool) $settings;
        }
        if(empty($UA)) {
            if (isset($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
                $UA = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            } elseif (isset($_SERVER['HTTP_USER_AGENT'])) {
                $UA = $_SERVER['HTTP_USER_AGENT'];
            } else {
                $UA = getenv('HTTP_USER_AGENT');
            }
        }
        if (empty($UA)) return false;
        $this->_set_browser('ua',$UA);
        if ($run) $this->init();
    }
    function init ()
    {
        $this->_get_ip();
        $this->_test_cookies();
        $this->_get_browser_info();
        $this->_get_gecko();
        $this->_get_languages();
        $this->_get_os_info();
        $this->_get_javascript();
        $this->_get_features();
        $this->_get_quirks();
    }
    function check_cookies($yn)
    {
        $this->_check_cookies = (bool) $yn;
    }
    function allow_masquerading($yn)
    {
        $this->_allow_masquerading = (bool) $yn;
    }
    function default_language($language)
    {
        $this->_default_language = $language;
    }
    function property ($p=null)
    {   if ($p==null) {
            return $this->_browser_info;
        } else {
            return $this->_browser_info[strtolower($p)];
        }
    }
    function get_property ($p)
    {
        return $this->property($p);
    }
    function is ($s)
    {
        if (preg_match('/l:'.$this->_language_search_regex.'/i',$s,$match)) {
            if ($match) return $this->_perform_language_search($match);
        }
        elseif (preg_match('/b:'.$this->_browser_search_regex.'/i',$s,$match)) {
            if ($match) return $this->_perform_browser_search($match);
        }
        return false;
    }
    function browser_is ($s)
    {
        preg_match('/'.$this->_browser_search_regex.'/i',$s,$match);
        if ($match) return $this->_perform_browser_search($match);
    }
    function language_is ($s)
    {
        preg_match('/'.$this->_language_search_regex.'/i',$s,$match);
        if ($match) return $this->_perform_language_search($match);
    }
    function has_feature ($s)
    {
        return $this->_feature_set[$s];
    }
    function has_quirk ($s)
    {
        return $this->_quirks[$s];
    }
    function _perform_browser_search ($data)
    {
        $search = array();
        $search['phrase']     = isset($data[0]) ? $data[0] : '';
        $search['name']       = isset($data[1]) ? strtolower($data[1]) : '';
        $search['maj_ver']    = isset($data[2]) ? $data[2] : '';
        $search['min_ver']    = isset($data[3]) ? $data[3] : '';
        $search['direction']  = isset($data[4]) ? strtolower($data[4]) : '';
        $looking_for = $search['maj_ver'].$search['min_ver'];
        if ($search['name'] == 'aol' || $search['name'] == 'webtv') {
            return stristr($this->_browser_info['ua'],$search['name']);
        } elseif ($this->_browser_info['browser'] == $search['name'] || $search['name'] == 'gecko') {
            if (strtolower($search['name']) == 'gecko') {
                $what_we_are =& $this->_browser_info['gecko_ver'];
            } else {
                $majv = $search['maj_ver'] ? $this->_browser_info['maj_ver'] : '';
                $minv = $search['min_ver'] ? $this->_browser_info['min_ver'] : '';
                $what_we_are = $majv.$minv;
            }
            if (($search['direction'] == 'up' || $search['direction'] == '+') && ($what_we_are >= $looking_for)) {
                return true;
            }
            elseif (($search['direction'] == 'dn' || $search['direction'] == '-') && ($what_we_are <= $looking_for)) {
                return true;
            }
            elseif ($what_we_are == $looking_for) {
                return true;
            }
        }
        return false;
    }
    function _perform_language_search ($data)
    {
        $this->_get_languages();
        return stristr($this->_browser_info['language'],$data[1]);
    }
    function _get_languages ()
    {
        if(!$this->_get_languages_ran_once) {
            if ($languages = getenv('HTTP_ACCEPT_LANGUAGE')) {
                $languages = preg_replace('/(;q=[0-9]+.[0-9]+)/i','',$languages);
            } else {
                $languages = $this->_default_language;
            }
            $this->_set_browser('language',$languages);
            $this->_get_languages_ran_once = true;
        }
    }
    function _get_os_info ()
    {
        $regex_windows  = '/([^dar]win[dows]*)[\s]?([0-9a-z]*)[\w\s]?([a-z0-9.]*)/i';
        $regex_mac      = '/(68[k0]{1,3})|(ppc mac os x)|([p\S]{1,5}pc)|(darwin)/i';
        $regex_os2      = '/os\/2|ibm-webexplorer/i';
        $regex_sunos    = '/(sun|i86)[os\s]*([0-9]*)/i';
        $regex_irix     = '/(irix)[\s]*([0-9]*)/i';
        $regex_hpux     = '/(hp-ux)[\s]*([0-9]*)/i';
        $regex_aix      = '/aix([0-9]*)/i';
        $regex_dec      = '/dec|osfl|alphaserver|ultrix|alphastation/i';
        $regex_vms      = '/vax|openvms/i';
        $regex_sco      = '/sco|unix_sv/i';
        $regex_linux    = '/x11|inux/i';
        $regex_bsd      = '/(free)?(bsd)/i';
        $regex_amiga    = '/amiga[os]?/i';
        if (preg_match_all($regex_windows,$this->_browser_info['ua'],$match)) {
            $v  = $match[2][count($match[0])-1];
            $v2 = $match[3][count($match[0])-1];
            if (stristr($v,'NT') && $v2 == 5.1) $v = 'xp';
            elseif ($v == '2000') $v = '2k';
            elseif (stristr($v,'NT') && $v2 == 5.0) $v = '2k';
            elseif (stristr($v,'9x') && $v2 == 4.9) $v = '98';
            elseif ($v.$v2 == '16bit') $v = '31';
            else $v .= $v2;
            if (empty($v)) $v = 'win';
            $this->_set_browser('os',strtolower($v));
            $this->_set_browser('platform','win');
        }
        elseif (preg_match($regex_amiga,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','amiga');
            if(stristr($this->_browser_info['ua'],'morphos')) {
                $this->_set_browser('os','morphos');
            }
            elseif (stristr($this->_browser_info['ua'],'mc680x0')) {
                $this->_set_browser('os','mc680x0');
            }
            elseif (stristr($this->_browser_info['ua'],'ppc')) {
                $this->_set_browser('os','ppc');
            }
            elseif (preg_match('/(AmigaOS [\.1-9]?)/i',$this->_browser_info['ua'],$match)) {
                $this->_set_browser('os',$match[1]);
            }
        }
        elseif ( preg_match($regex_os2,$this->_browser_info['ua'])) {
            $this->_set_browser('os','os2');
            $this->_set_browser('platform','os2');
        }
        elseif ( preg_match($regex_mac,$this->_browser_info['ua'],$match) ) {
            $this->_set_browser('platform','mac');
            $os = !empty($match[1]) ? '68k' : '';
            $os = !empty($match[2]) ? 'osx' : $os;
            $os = !empty($match[3]) ? 'ppc' : $os;
            $os = !empty($match[4]) ? 'osx' : $os;
            $this->_set_browser('os',$os);
        }
        elseif (preg_match($regex_sunos,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            if(!stristr('sun',$match[1])) $match[1] = 'sun'.$match[1];
            $this->_set_browser('os',$match[1].$match[2]);
        }
        elseif (preg_match($regex_irix,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os',$match[1].$match[2]);
        }
        elseif (preg_match($regex_hpux,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            $match[1] = str_replace('-','',$match[1]);
            $match[2] = (int) $match[2];
            $this->_set_browser('os',$match[1].$match[2]);
        }
        elseif (preg_match($regex_aix,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','aix'.$match[1]);
        }
        elseif (preg_match($regex_dec,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','dec');
        }
        elseif (preg_match($regex_vms,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','vms');
        }
        elseif (preg_match($regex_sco,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','sco');
        }
        elseif (stristr($this->_browser_info['ua'],'unix_system_v')) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','unixware');
        }
        elseif (stristr($this->_browser_info['ua'],'ncr')) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','mpras');
        }
        elseif (stristr($this->_browser_info['ua'],'reliantunix')) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','reliant');
        }
        elseif (stristr($this->_browser_info['ua'],'sinix')) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','sinix');
        }
        elseif (preg_match($regex_bsd,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os',$match[1].$match[2]);
        }
        elseif (preg_match($regex_linux,$this->_browser_info['ua'],$match)) {
            $this->_set_browser('platform','*nix');
            $this->_set_browser('os','linux');
        }
    }
    function _get_browser_info ()
    {
        $this->_build_regex();
        if(preg_match_all($this->_browser_regex,$this->_browser_info['ua'],$results)) {
            $count = count($results[0])-1;
            if($this->_allow_masquerading && $count > 0) $count--;
            $this->_set_browser('browser',$this->_get_short_name($results[1][$count]));
            $this->_set_browser('long_name',$results[1][$count]);
            $this->_set_browser('maj_ver',$results[2][$count]);
            preg_match('/([.\0-9]+)?([\.a-z0-9]+)?/i',$results[3][$count],$match);
            if (isset($match[1])) {
                $this->_set_browser('min_ver',$match[1]);
            } else {
                $this->_set_browser('min_ver','.0');
            }
            if (isset($match[2])) $this->_set_browser('letter_ver',$match[2]);
            $this->_set_browser('version',$this->_browser_info['maj_ver'].$this->property('min_ver'));
        }
    }
    function _get_ip()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
       $this->_set_browser('ip',$ip);
    }
    function _OLD_get_ip ()
    {   if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else {
            $ip = getenv('REMOTE_ADDR');
        }
        $this->_set_browser('ip',$ip);
    }
    function _build_regex ()
    {
        $browsers = '';
        while(list($k,) = each($this->_browsers)) {
            if (!empty($browsers)) $browsers .= "|";
            $browsers .= $k;
        }
        $version_string = "[\/\sa-z(]*([0-9]+)([\.0-9a-z]+)?";
        $this->_browser_regex = "/($browsers)$version_string/i";
    }
    function _get_short_name ($long_name)
    {
        return $this->_browsers[strtolower($long_name)];
    }
    function _test_cookies()
    {
        global $HTTP_COOKIE_VARS;
        $cookies = array();
        if (isset($_COOKIE)) {
            $cookies = $_COOKIE;
        } elseif (isset($HTTP_COOKIE_VARS)) {
            $cookies = $HTTP_COOKIE_VARS;
        }
        if ($this->_check_cookies) {
            $fp = @fopen($this->_temp_file_path.$this->property('ip'),'r');
            if (!$fp) {
                $fp = @fopen($this->_temp_file_path.$this->property('ip'),'a');
                if ($fp) {
                    fclose($fp);
                    setcookie('nqSniff_session','ss',0,'/');
                    setcookie('nqSniff_stored','st',time()+3600*24*365,'/');
                    $QS=getenv('QUERY_STRING');
                    $script_path=getenv('PATH_INFO')?getenv('PATH_INFO'):getenv('SCRIPT_NAME');
                    if (is_integer($pos=strpos(strrev($script_path),"php.xedni/"))&&!$pos) {
                        $script_path=strrev(substr(strrev($script_path),9));
                    }
                }
                $location='http://'.getenv('SERVER_NAME').$script_path.($QS==''?'':'?'.$QS);
                header("Location: $location");
                exit;
            } elseif ($fp) {
                unlink($this->_temp_file_path.$this->property('ip'));
                fclose($fp);
                $this->_set_browser('ss_cookies',isset($cookies['nqSniff_session'])?'true':'false');
                $this->_set_browser('st_cookies',isset($cookies['nqSniff_stored'])?'true':'false');
                setcookie('nqSniff_session','',0,'/');
                setcookie('nqSniff_stored','',0,'/');
            }
        }
    }
    function _get_javascript()
    {
        $set=false;
        while(list($version,$browser) = each($this->_javascript_versions)) {
            $browser = explode(',',$browser);
            while(list(,$search) = each($browser)) {
                if ($this->is('b:'.$search)) {
                    $this->_set_browser('javascript',$version);
                    $set = true;
                    break;
                }
            }
        if ($set) break;
        }
    }
    function _get_features ()
    {
        while(list($feature,$browser) = each($this->_browser_features)) {
            $browser = explode(',',$browser);
            while(list(,$search) = each($browser)) {
                if ($this->browser_is($search)) {
                    $this->_set_feature($feature);
                    break;
                }
            }
        }
    }
    function _get_quirks ()
    {
        while(list($quirk,$browser) = each($this->_browser_quirks)) {
            $browser = explode(',',$browser);
            while(list(,$search) = each($browser)) {
                if($this->browser_is($search)) {
                    $this->_set_quirk($quirk);
                    break;
                }
            }
        }
    }
    function _get_gecko ()
    {
        if(preg_match('/gecko\/([0-9]+)/i',$this->property('ua'),$match)) {
            $this->_set_browser('gecko',$match[1]);
            if (preg_match('/rv[: ]?([0-9a-z.+]+)/i',$this->property('ua'),$mozv)) {
                $this->_set_browser('gecko_ver',$mozv[1]);
            } elseif (preg_match('/(m[0-9]+)/i',$this->property('ua'),$mozv)) {
                $this->_set_browser('gecko_ver',$mozv[1]);
            }
            if($this->browser_is($this->_get_short_name('mozilla'))) {
                if(preg_match('/([0-9]+)([\.0-9]+)([a-z0-9+]?)/i',$mozv[1],$match)) {
                    $this->_set_browser('version',$mozv[1]);
                    $this->_set_browser('maj_ver',$match[1]);
                    $this->_set_browser('min_ver',$match[2]);
                    $this->_set_browser('letter_ver',$match[3]);
                }
            }
        } elseif ($this->is('b:'.$this->_get_short_name('mozilla'))) {
            $this->_set_browser('long_name','netscape');
            $this->_set_browser('browser',$this->_get_short_name('netscape'));
        }
    }
    function _set_browser ($k,$v)
    {
        $this->_browser_info[strtolower($k)] = strtolower($v);
    }
    function _set_feature ($k)
    {
        $this->_feature_set[strtolower($k)] = !$this->_feature_set[strtolower($k)];
    }
    function _set_quirk ($k)
    {
        $this->_quirks[strtolower($k)] = true;
    }
}
?>