<?php
 
function menutree_userapi_link($args) {

	$delimiter = '|';
	
	extract($args);

	$info = explode($delimiter,$link);
	$data['text'] = $info[0];
	if (isset($info[1]) && strtoupper(trim($info[1])) != "NULL") {
		$data['url'] = trim($info[1]);

		if (is_numeric($data['url'])) {
			if (xarMod::IsAvailable('path')) {
				$data['url'] = xarMod::apiFunc('path','user','itemurl',array('itemid' => $data['url']));
			} else {
				$data['url'] = xarModURL('content','user','display',array('itemid' => $data['url']));
			}
		} 
		if (substr($data['url'],0,2) == 'c:') {
			$url = explode(':',$data['url']);
			if (is_numeric($url[1])) { 
				$type = 'user';
				$func = 'display'; 
				array_unshift($url,'placekeeper','placekeeper');
			} else {
				$type = $url[1];
				$func = $url[2]; 
			}
			if (isset($url[3]) && strtoupper($url[3]) != 'NULL') {
				$arr['itemid'] = $url[3];
			}
			if (isset($url[4]) && strtoupper($url[4]) != 'NULL') {
				$arr['ctype'] = $url[4];
			}
			if (isset($url[5]) && strtoupper($url[5]) != 'NULL') {
				$extra = explode(';',$url[5]);
				foreach($extra as $key=>$val) {
					$pair = explode(',',$val);
					$the_key = $pair[0];
					$the_val = $pair[1];
					$arr[$the_key] = $the_val;
				}
			}
			if (xarMod::IsAvailable('path')) { 
				$arr['module'] = 'content';
				if ($func != 'display') {
					$arr['func'] = $func;
				} 
				$path = xarMod::apiFunc('path','user','checkaction',array('action' => $arr));
				foreach ($path as $key=>$val) {
					$str = xarServer::getBaseURL();
					$last = $str[strlen($str)-1];
					$first = $val[0];
					if ($last == '/' && $first == '/') {
						$val = substr($val,1); 
					} 
					$data['url'] = $str . $val;
				}
			} else {
				$data['url'] = xarModURL('content',$type,$func,$arr);
			}
		}

		unset($arr);

		// a short URL format for articles, e.g. a:user:display:$aid:$ptid:param,value;param2,value2 etc
		// This has not been tested much with the articles module
		if (substr($data['url'],0,2) == 'a:') {
			$url = explode(':',$data['url']);
			if (is_numeric($url[1])) { 
				$type = 'user';
				$func = 'display'; 
				array_unshift($url,'placekeeper','placekeeper');
			} else {
				$type = $url[1];
				$func = $url[2]; 
			}
			if (isset($url[3]) && strtoupper($url[3]) != 'NULL') {
				$arr['aid'] = $url[3];
			}
			if (isset($url[4]) && strtoupper($url[4]) != 'NULL') {
				$arr['ptid'] = $url[4];
			}
			if (isset($url[5]) && strtoupper($url[5]) != 'NULL') {
				$extra = explode(';',$url[5]);
				foreach($extra as $key=>$val) {
					$pair = explode(',',$val);
					$the_key = $pair[0];
					$the_val = $pair[1];
					$arr[$the_key] = $the_val;
				}
			}
			if (xarMod::IsAvailable('path')) { 
				$arr['module'] = 'articles';
				if ($func != 'display') {
					$arr['func'] = $func;
				} 
				$path = xarMod::apiFunc('path','user','checkaction',array('action' => $arr));
				foreach ($path as $key=>$val) {
					$str = xarServer::getBaseURL();
					$last = $str[strlen($str)-1];
					$first = $val[0];
					if ($last == '/' && $first == '/') {
						$val = substr($val,1); 
					} 
					$data['url'] = $str . $val;
				}
			} else {
				$data['url'] = xarModURL('articles',$type,$func,$arr);
			}
		}

	} else {
		$data['url'] = xarServer::getCurrentURL(). '#';
	}
	if (isset($info[2])) {
		$data['status'] = $info[2];
	} else {
		$data['status'] = '1';
	}
	
	return $data;

}

?>