<?php
function netquery_admin_xageoip()
{
    if (!xarSecurityCheck('EditRole')) return;
    if (!xarVarFetch('step', 'int:1:100', $step, '1', XARVAR_NOT_REQUIRED)) return;
    $data = array();
    $data['step'] = $step;
    switch ($step) {
        case '1':
        default:
            $data['body'] = '<br /><br />The first step in building a new table is to delete the existing geoip table along with the related geocc table.';
            $data['body'] .= ' Unless it has been backed up, all of the data contained in both tables will be lost.';
            $data['body'] .= '<br /><br />Do you wish to proceed?:';
            $data['body'] .= ' [<a href="'.xarModURL('netquery', 'admin', 'xageoip', array('step' => 99)).'">Yes</a>]';
            $data['body'] .= ' [<a href="'.xarModURL('netquery', 'admin', 'config').'">No</a>]<br /><br />';
            return $data;
            break;
        case '99':
            $dbconn =& xarDBGetConn();
//          $xartable =& xarDBGetTables();
//          $GeoccTable = $xartable['netquery_geocc'];
//          $GeoipTable = $xartable['netquery_geoip'];
            $GeoccTable = xarDBGetSiteTablePrefix() . '_netquery_geocc';
            $GeoipTable = xarDBGetSiteTablePrefix() . '_netquery_geoip';
            xarDBLoadTableMaintenanceAPI();
            $query = xarDBDropTable($GeoccTable);
            $result = &$dbconn->Execute($query);
            if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
              xarErrorHandled();
            }
            $query = xarDBDropTable($GeoipTable);
            $result = &$dbconn->Execute($query);
            if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
              xarErrorHandled();
            }
            $geoccfields = array(
                 'ci' => array('type'=>'integer','size'=>'tiny','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
                ,'cc' => array('type'=>'char','size'=>2,'null'=>FALSE,'default'=>'')
                ,'cn' => array('type'=>'varchar','size'=>50,'null'=>FALSE,'default'=>''));
            $query = xarDBCreateTable($GeoccTable,$geoccfields);
            $result = &$dbconn->Execute($query);
            $geoipfields = array(
                 'start' => array('type'=>'integer','size'=>10,'unsigned'=>TRUE,'null'=>FALSE,'default'=>'0')
                ,'end'   => array('type'=>'integer','size'=>10,'unsigned'=>TRUE,'null'=>FALSE,'default'=>'0')
                ,'ci'    => array('type'=>'integer','size'=>'tiny','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'));
            $query = xarDBCreateTable($GeoipTable,$geoipfields);
            $result = &$dbconn->Execute($query);
$geoccitems = array(
array(1, 'GB', 'United Kingdom'),
array(2, 'US', 'United States'),
array(3, 'CA', 'Canada'),
array(4, 'MX', 'Mexico'),
array(5, 'BM', 'Bermuda'),
array(6, 'SE', 'Sweden'),
array(7, 'IT', 'Italy'),
array(8, 'CH', 'Switzerland'),
array(9, 'PR', 'Puerto Rico'),
array(10, 'IN', 'India'),
array(11, 'VI', 'Virgin Islands'),
array(12, 'DE', 'Germany'),
array(13, 'IR', 'Iran'),
array(14, 'BO', 'Bolivia'),
array(15, 'NG', 'Nigeria'),
array(16, 'NL', 'Netherlands'),
array(17, 'FR', 'France'),
array(18, 'JP', 'Japan'),
array(19, 'IL', 'Israel'),
array(20, 'DK', 'Denmark'),
array(21, 'AU', 'Australia'),
array(22, 'ES', 'Spain'),
array(23, 'CL', 'Chile'),
array(24, 'BS', 'Bahamas'),
array(25, 'AR', 'Argentina'),
array(26, 'DM', 'Dominica'),
array(27, 'BZ', 'Belize'),
array(28, 'BR', 'Brazil'),
array(29, 'IE', 'Ireland'),
array(30, 'BE', 'Belgium'),
array(31, 'SG', 'Singapore'),
array(32, 'CN', 'China'),
array(33, 'PT', 'Portugal'),
array(34, 'HK', 'Hong Kong'),
array(35, 'SN', 'Senegal'),
array(36, 'TH', 'Thailand'),
array(37, 'KR', 'Korea'),
array(38, 'TW', 'Taiwan'),
array(39, 'BD', 'Bangladesh'),
array(40, 'MY', 'Malaysia'),
array(41, 'NZ', 'New Zealand'),
array(42, 'ID', 'Indonesia'),
array(43, 'PK', 'Pakistan'),
array(44, 'BN', 'Brunei Darussalam'),
array(45, 'PH', 'Philippines'),
array(46, 'AP', 'Asia/Pacific Region'),
array(47, 'GR', 'Greece'),
array(48, 'SA', 'Saudi Arabia'),
array(49, 'PL', 'Poland'),
array(50, 'AT', 'Austria'),
array(51, 'CZ', 'Czech Republic'),
array(52, 'RU', 'Russian Federation'),
array(53, 'KE', 'Kenya'),
array(54, 'A2', 'Satellite Provider'),
array(55, 'EG', 'Egypt'),
array(56, 'NO', 'Norway'),
array(57, 'UA', 'Ukraine'),
array(58, 'TJ', 'Tajikistan'),
array(59, 'TR', 'Turkey'),
array(60, 'GE', 'Georgia'),
array(61, 'BY', 'Belarus'),
array(62, 'IQ', 'Iraq'),
array(63, 'AM', 'Armenia'),
array(64, 'LB', 'Lebanon'),
array(65, 'MD', 'Moldova'),
array(66, 'BG', 'Bulgaria'),
array(67, 'FI', 'Finland'),
array(68, 'CM', 'Cameroon'),
array(69, 'GH', 'Ghana'),
array(70, 'ZW', 'Zimbabwe'),
array(71, 'CD', 'Congo'),
array(72, 'MG', 'Madagascar'),
array(73, 'CI', "Cote D'Ivoire"),
array(74, 'BJ', 'Benin'),
array(75, 'RW', 'Rwanda'),
array(76, 'TG', 'Togo'),
array(77, 'MZ', 'Mozambique'),
array(78, 'GN', 'Guinea'),
array(79, 'ZM', 'Zambia'),
array(80, 'TZ', 'Tanzania'),
array(81, 'AO', 'Angola'),
array(82, 'OM', 'Oman'),
array(83, 'DZ', 'Algeria'),
array(84, 'BV', 'Bouvet Island'),
array(85, 'EU', 'Europe'),
array(86, 'EE', 'Estonia'),
array(87, 'SK', 'Slovakia'),
array(88, 'LY', 'Libyan Arab Jamahiriya'),
array(89, 'JO', 'Jordan'),
array(90, 'HU', 'Hungary'),
array(91, 'KW', 'Kuwait'),
array(92, 'AL', 'Albania'),
array(93, 'LT', 'Lithuania'),
array(94, 'LV', 'Latvia'),
array(95, 'SZ', 'Swaziland'),
array(96, 'SM', 'San Marino'),
array(97, 'RO', 'Romania'),
array(98, 'YU', 'Yugoslavia'),
array(99, 'KZ', 'Kazakstan'),
array(100, 'LU', 'Luxembourg'),
array(101, 'AF', 'Afghanistan'),
array(102, 'MA', 'Morocco'),
array(103, 'IS', 'Iceland'),
array(104, 'CR', 'Costa Rica'),
array(105, 'CY', 'Cyprus'),
array(106, 'MK', 'Macedonia'),
array(107, 'MT', 'Malta'),
array(108, 'ZA', 'South Africa'),
array(109, 'MW', 'Malawi'),
array(110, 'SI', 'Slovenia'),
array(111, 'HR', 'Croatia'),
array(112, 'AZ', 'Azerbaijan'),
array(113, 'MC', 'Monaco'),
array(114, 'HT', 'Haiti'),
array(115, 'SV', 'El Salvador'),
array(116, 'GU', 'Guam'),
array(117, 'JM', 'Jamaica'),
array(118, 'FM', 'Micronesia'),
array(119, 'EC', 'Ecuador'),
array(120, 'CO', 'Colombia'),
array(121, 'UG', 'Uganda'),
array(122, 'LR', 'Liberia'),
array(123, 'PE', 'Peru'),
array(124, 'TF', 'French Southern Territories'),
array(125, 'AN', 'Netherlands Antilles'),
array(126, 'HN', 'Honduras'),
array(127, 'SL', 'Sierra Leone'),
array(128, 'GW', 'Guinea-Bissau'),
array(129, 'YE', 'Yemen'),
array(130, 'VG', 'Virgin Islands'),
array(131, 'LC', 'Saint Lucia'),
array(132, 'SY', 'Syrian Arab Republic'),
array(133, 'DO', 'Dominican Republic'),
array(134, 'NI', 'Nicaragua'),
array(135, 'GT', 'Guatemala'),
array(136, 'VE', 'Venezuela'),
array(137, 'MN', 'Mongolia'),
array(138, 'TT', 'Trinidad and Tobago'),
array(139, 'AS', 'American Samoa'),
array(140, 'PA', 'Panama'),
array(141, 'AE', 'United Arab Emirates'),
array(142, 'MH', 'Marshall Islands'),
array(143, 'BH', 'Bahrain'),
array(144, 'CK', 'Cook Islands'),
array(145, 'GI', 'Gibraltar'),
array(146, 'PY', 'Paraguay'),
array(147, 'AG', 'Antigua and Barbuda'),
array(148, 'KN', 'Saint Kitts and Nevis'),
array(149, 'GL', 'Greenland'),
array(150, 'PW', 'Palau'),
array(151, 'SR', 'Suriname'),
array(152, 'BB', 'Barbados'),
array(153, 'GY', 'Guyana'),
array(154, 'PF', 'French Polynesia'),
array(155, 'MR', 'Mauritania'),
array(156, 'ML', 'Mali'),
array(157, 'CG', 'Congo'),
array(158, 'SC', 'Seychelles'),
array(159, 'LK', 'Sri Lanka'),
array(160, 'BW', 'Botswana'),
array(161, 'UZ', 'Uzbekistan'),
array(162, 'MO', 'Macau'),
array(163, 'QA', 'Qatar'),
array(164, 'WF', 'Wallis and Futuna'),
array(165, 'NA', 'Namibia'),
array(166, 'KH', 'Cambodia'),
array(167, 'BA', 'Bosnia and Herzegovina'),
array(168, 'CU', 'Cuba'),
array(169, 'UM', 'United States Minor Outlying Islands'),
array(170, 'HM', 'Heard Island and McDonald Islands'),
array(171, 'VN', 'Vietnam'),
array(172, 'NE', 'Niger'),
array(173, 'VU', 'Vanuatu'),
array(174, 'TC', 'Turks and Caicos Islands'),
array(175, 'UY', 'Uruguay'),
array(176, 'NC', 'New Caledonia'),
array(177, 'MP', 'Northern Mariana Islands'),
array(178, 'AI', 'Anguilla'),
array(179, 'GD', 'Grenada'),
array(180, 'MS', 'Montserrat'),
array(181, 'GP', 'Guadeloupe'),
array(182, 'MU', 'Mauritius'),
array(183, 'TD', 'Chad'),
array(184, 'LI', 'Liechtenstein'),
array(185, 'RE', 'Reunion'),
array(186, 'FO', 'Faroe Islands'),
array(187, 'SO', 'Somalia'),
array(188, 'GM', 'Gambia'),
array(189, 'GA', 'Gabon'),
array(190, 'KM', 'Comoros'),
array(191, 'LA', "Lao People's Democratic Republic"),
array(192, 'BI', 'Burundi'),
array(193, 'TN', 'Tunisia'),
array(194, 'MQ', 'Martinique'),
array(195, 'KG', 'Kyrgyzstan'),
array(196, 'VA', 'Holy See (Vatican City State)'),
array(197, 'PS', 'Palestinian Territory'),
array(198, 'ET', 'Ethiopia'),
array(199, 'AD', 'Andorra'),
array(200, 'AQ', 'Antarctica'),
array(201, 'FJ', 'Fiji'),
array(202, 'BF', 'Burkina Faso'),
array(203, 'PG', 'Papua New Guinea'),
array(204, 'SD', 'Sudan'),
array(205, 'CF', 'Central African Republic'),
array(206, 'DJ', 'Djibouti'),
array(207, 'GQ', 'Equatorial Guinea'),
array(208, 'TM', 'Turkmenistan'),
array(209, 'CV', 'Cape Verde'),
array(210, 'ST', 'Sao Tome and Principe'),
array(211, 'FK', 'Falkland Islands (Malvinas)'),
array(212, 'VC', 'Saint Vincent and the Grenadines'),
array(213, 'LS', 'Lesotho'),
array(214, 'BT', 'Bhutan'),
array(215, 'ER', 'Eritrea'),
array(216, 'AW', 'Aruba'),
array(217, 'SB', 'Solomon Islands'),
array(218, 'MV', 'Maldives'),
array(219, 'TV', 'Tuvalu'),
array(220, 'WS', 'Samoa'),
array(221, 'KI', 'Kiribati'),
array(222, 'IO', 'British Indian Ocean Territory'),
array(223, 'NP', 'Nepal'),
array(224, 'TO', 'Tonga'),
array(225, 'NR', 'Nauru'),
array(226, 'TK', 'Tokelau'),
array(227, 'NF', 'Norfolk Island'),
array(228, 'MM', 'Myanmar'),
array(229, 'KY', 'Cayman Islands'),
array(230, 'KP', 'Korea'),
array(231, 'GF', 'French Guiana'),
array(232, 'YT', 'Mayotte'),
array(233, 'Z0', 'Reserved Addr'),
array(234, 'Z1', 'Loopback Addr'),
array(235, 'Z2', 'DHCP Address'),
array(236, 'Z3', 'Private Addr'));
            foreach ($geoccitems as $geoccitem) {
                list($ci,$cc,$cn) = $geoccitem;
                $query = "INSERT INTO $GeoccTable
                        (ci, cc, cn)
                        VALUES (?,?,?)";
                $bindvars = array((int)$ci, (string)$cc, (string)$cn);
                $result =& $dbconn->Execute($query,$bindvars);
            }
$geoipitems = array(
array(0,16777215,233),
array(167772160,184549375,236),
array(2130706432,2147483647,234),
array(2147483648,2147549183,233),
array(2851995648,2852061183,235),
array(2886729728,2887778303,236),
array(3221159936,3221225471,233),
array(3221225472,3221225727,233),
array(3232235520,3232301055,236),
array(3758096128,3758096383,233),
array(33996344, 33996351, 1),
array(50331648, 67277055, 2),
array(67277056, 67277119, 3),
array(67277120, 67283519, 2),
array(67283520, 67283583, 3),
array(67283584, 68257567, 2),
array(68257568, 68257599, 3),
array(68257600, 68259583, 2),
array(68259584, 68259599, 3),
array(68259600, 68296775, 2),
array(68296776, 68296783, 4),
array(68296784, 68298887, 2),
array(68298888, 68298895, 3),
array(68298896, 68305407, 2),
array(68305408, 68305919, 4),
array(68305920, 68314143, 2),
array(68314144, 68314151, 3),
array(68314152, 68395663, 2),
array(68395664, 68395671, 3),
array(68395672, 68438287, 2),
array(68438288, 68438303, 3),
array(68438304, 68527287, 2),
array(68527288, 68527295, 3),
array(68527296, 68637311, 2),
array(68637312, 68637375, 3),
array(68637376, 68657503, 2),
array(68657504, 68657519, 3),
array(68657520, 69533951, 2),
array(69533952, 69534207, 3),
array(69534208, 69915111, 2),
array(69915112, 69915119, 3),
array(69915120, 69952575, 2),
array(69952576, 69952639, 3),
array(69952640, 69956103, 2),
array(69956104, 69956111, 5),
array(69956112, 83886079, 2),
array(94585424, 94585439, 6));
            foreach ($geoipitems as $geoipitem) {
                list($start,$end,$ci) = $geoipitem;
                $query = "INSERT INTO $GeoipTable
                        (start, end, ci)
                        VALUES (?,?,?)";
                $bindvars = array($start, $end, (int)$ci);
                $result =& $dbconn->Execute($query,$bindvars);
            }
            xarResponseRedirect(xarModURL('netquery', 'admin', 'xageoip', array('step' => '2')));
            return true;
            break;
        case '2':
            $data['body'] = '<br /><br />New geoip and geocc tables have been created and geocc has been populated with initial entries.';
            $data['body'] = '<br /><br />Populate geoip table: &curren;&curren;';
//          $data['body'] .= '<br />[<a href="'.xarModURL('netquery', 'admin', 'xageoip2', array('step' => '2')).'">'.long2ip(100663296).' to '.long2ip(3741319167).'</a>]';
            $data['body'] .= '<br />[<a href="'.xarModURL('netquery', 'admin', 'xageoip2', array('step' => '2')).'">'.long2ip(100663296).' to '.long2ip(1113216991).'</a>]';
            return $data;
            break;
        case '3':
            $data['body'] = '<br /><br />Populating geoip table: &curren;&curren;&curren;';
            $data['body'] .= '<br /><br />[<a href="'.xarModURL('netquery', 'admin', 'xageoip3', array('step' => '3')).'">'.long2ip(1113216992).' to '.long2ip(2642214911).'</a>]';
//          xarResponseRedirect(xarModURL('netquery', 'admin', 'xageoip3', array('step' => '3')));
            return $data;
            break;
        case '4':
            $data['body'] = '<br /><br />Populating geoip table: &curren;&curren;&curren;&curren;';
            $data['body'] .= '<br /><br />[<a href="'.xarModURL('netquery', 'admin', 'xageoip4', array('step' => '4')).'">'.long2ip(2642214912).' to '.long2ip(3262477411).'</a>]';
//          xarResponseRedirect(xarModURL('netquery', 'admin', 'xageoip4', array('step' => '4')));
            return $data;
            break;
        case '5':
            $data['body'] = '<br /><br />Populating geoip table: &curren;&curren;&curren;&curren;&curren;';
            $data['body'] .= '<br /><br />[<a href="'.xarModURL('netquery', 'admin', 'xageoip5', array('step' => '5')).'">'.long2ip(3262477412).' to '.long2ip(3278945283).'</a>]';
//          xarResponseRedirect(xarModURL('netquery', 'admin', 'xageoip5', array('step' => '5')));
            return $data;
            break;
        case '6':
            $data['body'] = '<br /><br />Populating geoip table: &curren;&curren;&curren;&curren;&curren;&curren;';
            $data['body'] .= '<br /><br />[<a href="'.xarModURL('netquery', 'admin', 'xageoip6', array('step' => '6')).'">'.long2ip(3278945284).' to '.long2ip(3453072787).'</a>]';
//          xarResponseRedirect(xarModURL('netquery', 'admin', 'xageoip6', array('step' => '6')));
            return $data;
            break;
        case '7':
            $data['body'] = '<br /><br />Populating geoip table: &curren;&curren;&curren;&curren;&curren;&curren;&curren;';
            $data['body'] .= '<br /><br />[<a href="'.xarModURL('netquery', 'admin', 'xageoip7', array('step' => '7')).'">'.long2ip(3453072788).' to '.long2ip(3564429311).'</a>]';
//          xarResponseRedirect(xarModURL('netquery', 'admin', 'xageoip7', array('step' => '7')));
            return $data;
            break;
        case '8':
            $data['body'] = '<br /><br />Populating geoip table: &curren;&curren;&curren;&curren;&curren;&curren;&curren;&curren;';
            $data['body'] .= '<br /><br />[<a href="'.xarModURL('netquery', 'admin', 'xageoip8', array('step' => '8')).'">'.long2ip(3564429312).' to '.long2ip(3741319167).'</a>]';
//          xarResponseRedirect(xarModURL('netquery', 'admin', 'xageoip8', array('step' => '8')));
            return $data;
            break;
        case '9':
            $data['body'] = '<br /><br />Process completed. The new geoip and geocc tables have now been fully populated.';
            $data['body'] .= '<br /><br />Please click <a href="'.xarModURL('netquery', 'admin', 'config').'">HERE</a> to return to Netquery\'s main admin panel.<br /><br />';
            return $data;
            break;
    }
    return true;
}
?>