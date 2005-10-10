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
                 'ci'    => array('type'=>'integer','size'=>'tiny','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
                ,'cc'    => array('type'=>'char','size'=>2,'null'=>FALSE,'default'=>'')
                ,'cn'    => array('type'=>'varchar','size'=>50,'null'=>FALSE,'default'=>'')
                ,'lat'   => array('type'=>'float','size'=>'decimal','width'=>'7','decimals'=>'4','null'=>FALSE,'default'=>'0.0000')
                ,'lon'   => array('type'=>'float','size'=>'decimal','width'=>'7','decimals'=>'4','null'=>FALSE,'default'=>'0.0000')
                ,'users' => array('type'=>'integer','size'=>'medium','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'));
            $query = xarDBCreateTable($GeoccTable,$geoccfields);
            $result = &$dbconn->Execute($query);
            $geoipfields = array(
                 'start' => array('type'=>'integer','size'=>10,'unsigned'=>TRUE,'null'=>FALSE,'default'=>'0')
                ,'end'   => array('type'=>'integer','size'=>10,'unsigned'=>TRUE,'null'=>FALSE,'default'=>'0')
                ,'ci'    => array('type'=>'integer','size'=>'tiny','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'));
            $query = xarDBCreateTable($GeoipTable,$geoipfields);
            $result = &$dbconn->Execute($query);
$geoccitems = array(
array(1, 'UK', 'United Kingdom', 54.0000, -2.0000, 0),
array(2, 'US', 'United States', 38.0000, -97.0000, 0),
array(3, 'CA', 'Canada', 60.0000, -95.0000, 0),
array(4, 'MX', 'Mexico', 23.0000, -102.0000, 0),
array(5, 'BM', 'Bermuda', 32.3333, -64.7500, 0),
array(6, 'SE', 'Sweden', 62.0000, 15.0000, 0),
array(7, 'IT', 'Italy', 42.8333, 12.8333, 0),
array(8, 'CH', 'Switzerland', 47.0000, 8.0000, 0),
array(9, 'PR', 'Puerto Rico', 18.2500, -66.5000, 0),
array(10, 'IN', 'India', 20.0000, 77.0000, 0),
array(11, 'VI', 'Virgin Islands', 18.3333, -64.8333, 0),
array(12, 'DE', 'Germany', 51.0000, 9.0000, 0),
array(13, 'IR', 'Iran', 32.0000, 53.0000, 0),
array(14, 'BO', 'Bolivia', -17.0000, -65.0000, 0),
array(15, 'NG', 'Nigeria', 10.0000, 8.0000, 0),
array(16, 'NL', 'Netherlands', 52.5000, 5.7500, 0),
array(17, 'FR', 'France', 46.0000, 2.0000, 0),
array(18, 'JP', 'Japan', 36.0000, 138.0000, 0),
array(19, 'IL', 'Israel', 31.5000, 34.7500, 0),
array(20, 'DK', 'Denmark', 56.0000, 10.0000, 0),
array(21, 'AU', 'Australia', -27.0000, 133.0000, 0),
array(22, 'ES', 'Spain', 40.0000, -4.0000, 0),
array(23, 'CL', 'Chile', -30.0000, -71.0000, 0),
array(24, 'BS', 'Bahamas', 24.2500, -76.0000, 0),
array(25, 'AR', 'Argentina', -34.0000, -64.0000, 0),
array(26, 'DM', 'Dominica', 15.4167, -61.3333, 0),
array(27, 'BZ', 'Belize', 17.2500, -88.7500, 0),
array(28, 'BR', 'Brazil', -10.0000, -55.0000, 0),
array(29, 'IE', 'Ireland', 53.0000, -8.0000, 0),
array(30, 'BE', 'Belgium', 50.8333, 4.0000, 0),
array(31, 'SG', 'Singapore', 1.3667, 103.8000, 0),
array(32, 'CN', 'China', 35.0000, 105.0000, 0),
array(33, 'PT', 'Portugal', 39.5000, -8.0000, 0),
array(34, 'HK', 'Hong Kong', 22.2500, 114.1667, 0),
array(35, 'SN', 'Senegal', 14.0000, -14.0000, 0),
array(36, 'TH', 'Thailand', 15.0000, 100.0000, 0),
array(37, 'KR', 'Korea', 37.0000, 127.5000, 0),
array(38, 'TW', 'Taiwan', 23.5000, 121.0000, 0),
array(39, 'BD', 'Bangladesh', 24.0000, 90.0000, 0),
array(40, 'MY', 'Malaysia', 2.5000, 112.5000, 0),
array(41, 'NZ', 'New Zealand', -41.0000, 174.0000, 0),
array(42, 'ID', 'Indonesia', -5.0000, 120.0000, 0),
array(43, 'PK', 'Pakistan', 30.0000, 70.0000, 0),
array(44, 'BN', 'Brunei Darussalam', 4.5000, 114.6667, 0),
array(45, 'PH', 'Philippines', 13.0000, 122.0000, 0),
array(46, 'AP', 'Asia-Pacific Region', 35.0000, 105.0000, 0),
array(47, 'GR', 'Greece', 39.0000, 22.0000, 0),
array(48, 'SA', 'Saudi Arabia', 25.0000, 45.0000, 0),
array(49, 'PL', 'Poland', 52.0000, 20.0000, 0),
array(50, 'AT', 'Austria', 47.3333, 13.3333, 0),
array(51, 'CZ', 'Czech Republic', 49.7500, 15.5000, 0),
array(52, 'RU', 'Russian Federation', 60.0000, 100.0000, 0),
array(53, 'KE', 'Kenya', 1.0000, 38.0000, 0),
array(54, 'A2', 'Satellite Provider', 0.0000, 0.0000, 0),
array(55, 'EG', 'Egypt', 27.0000, 30.0000, 0),
array(56, 'NO', 'Norway', 62.0000, 10.0000, 0),
array(57, 'UA', 'Ukraine', 49.0000, 32.0000, 0),
array(58, 'TJ', 'Tajikistan', 39.0000, 71.0000, 0),
array(59, 'TR', 'Turkey', 39.0000, 35.0000, 0),
array(60, 'GE', 'Georgia', 42.0000, 43.5000, 0),
array(61, 'BY', 'Belarus', 53.0000, 28.0000, 0),
array(62, 'IQ', 'Iraq', 33.0000, 44.0000, 0),
array(63, 'AM', 'Armenia', 40.0000, 45.0000, 0),
array(64, 'LB', 'Lebanon', 33.8333, 35.8333, 0),
array(65, 'MD', 'Moldova', 47.0000, 29.0000, 0),
array(66, 'BG', 'Bulgaria', 43.0000, 25.0000, 0),
array(67, 'FI', 'Finland', 64.0000, 26.0000, 0),
array(68, 'CM', 'Cameroon', 6.0000, 12.0000, 0),
array(69, 'GH', 'Ghana', 8.0000, -2.0000, 0),
array(70, 'ZW', 'Zimbabwe', -20.0000, 30.0000, 0),
array(71, 'CD', 'Congo', 0.0000, 25.0000, 0),
array(72, 'MG', 'Madagascar', -20.0000, 47.0000, 0),
array(73, 'CI', 'Cote D\'Ivoire', 8.0000, -5.0000, 0),
array(74, 'BJ', 'Benin', 9.5000, 2.2500, 0),
array(75, 'RW', 'Rwanda', -2.0000, 30.0000, 0),
array(76, 'TG', 'Togo', 8.0000, 1.1667, 0),
array(77, 'MZ', 'Mozambique', -18.2500, 35.0000, 0),
array(78, 'GN', 'Guinea', 11.0000, -10.0000, 0),
array(79, 'ZM', 'Zambia', -15.0000, 30.0000, 0),
array(80, 'TZ', 'Tanzania', -6.0000, 35.0000, 0),
array(81, 'AO', 'Angola', -12.5000, 18.5000, 0),
array(82, 'OM', 'Oman', 21.0000, 57.0000, 0),
array(83, 'DZ', 'Algeria', 28.0000, 3.0000, 0),
array(84, 'BV', 'Bouvet Island', -54.4333, 3.4000, 0),
array(85, 'EU', 'Europe', 47.0000, 8.0000, 0),
array(86, 'EE', 'Estonia', 59.0000, 26.0000, 0),
array(87, 'SK', 'Slovakia', 48.6667, 19.5000, 0),
array(88, 'LY', 'Libyan Arab Jamahiriya', 25.0000, 17.0000, 0),
array(89, 'JO', 'Jordan', 31.0000, 36.0000, 0),
array(90, 'HU', 'Hungary', 47.0000, 20.0000, 0),
array(91, 'KW', 'Kuwait', 29.5000, 45.7500, 0),
array(92, 'AL', 'Albania', 41.0000, 20.0000, 0),
array(93, 'LT', 'Lithuania', 56.0000, 24.0000, 0),
array(94, 'LV', 'Latvia', 57.0000, 25.0000, 0),
array(95, 'SZ', 'Swaziland', -26.5000, 31.5000, 0),
array(96, 'SM', 'San Marino', 43.7667, 12.4167, 0),
array(97, 'RO', 'Romania', 46.0000, 25.0000, 0),
array(98, 'YU', 'Yugoslavia', 44.0000, 21.0000, 0),
array(99, 'KZ', 'Kazakstan', 48.0000, 68.0000, 0),
array(100, 'LU', 'Luxembourg', 49.7500, 6.1667, 0),
array(101, 'AF', 'Afghanistan', 33.0000, 65.0000, 0),
array(102, 'MA', 'Morocco', 32.0000, -5.0000, 0),
array(103, 'IS', 'Iceland', 65.0000, -18.0000, 0),
array(104, 'CR', 'Costa Rica', 10.0000, -84.0000, 0),
array(105, 'CY', 'Cyprus', 35.0000, 33.0000, 0),
array(106, 'MK', 'Macedonia', 41.8333, 22.0000, 0),
array(107, 'MT', 'Malta', 35.8333, 14.5833, 0),
array(108, 'ZA', 'South Africa', -29.0000, 24.0000, 0),
array(109, 'MW', 'Malawi', -13.5000, 34.0000, 0),
array(110, 'SI', 'Slovenia', 46.0000, 15.0000, 0),
array(111, 'HR', 'Croatia', 45.1667, 15.5000, 0),
array(112, 'AZ', 'Azerbaijan', 40.5000, 47.5000, 0),
array(113, 'MC', 'Monaco', 43.7333, 7.4000, 0),
array(114, 'HT', 'Haiti', 19.0000, -72.4167, 0),
array(115, 'SV', 'El Salvador', 13.8333, -88.9167, 0),
array(116, 'GU', 'Guam', 13.4667, 144.7833, 0),
array(117, 'JM', 'Jamaica', 18.2500, -77.5000, 0),
array(118, 'FM', 'Micronesia', 6.9167, 158.2500, 0),
array(119, 'EC', 'Ecuador', -2.0000, -77.5000, 0),
array(120, 'CO', 'Colombia', 4.0000, -72.0000, 0),
array(121, 'UG', 'Uganda', 1.0000, 32.0000, 0),
array(122, 'LR', 'Liberia', 6.5000, -9.5000, 0),
array(123, 'PE', 'Peru', -10.0000, -76.0000, 0),
array(124, 'TF', 'French Southern Territories', -43.0000, 67.0000, 0),
array(125, 'AN', 'Netherlands Antilles', 12.2500, -68.7500, 0),
array(126, 'HN', 'Honduras', 15.0000, -86.5000, 0),
array(127, 'SL', 'Sierra Leone', 8.5000, -11.5000, 0),
array(128, 'GW', 'Guinea-Bissau', 12.0000, -15.0000, 0),
array(129, 'YE', 'Yemen', 15.0000, 48.0000, 0),
array(130, 'VG', 'British Virgin Islands', 18.5000, -64.5000, 0),
array(131, 'LC', 'Saint Lucia', 13.8833, -61.1333, 0),
array(132, 'SY', 'Syrian Arab Republic', 35.0000, 38.0000, 0),
array(133, 'DO', 'Dominican Republic', 19.0000, -70.6667, 0),
array(134, 'NI', 'Nicaragua', 13.0000, -85.0000, 0),
array(135, 'GT', 'Guatemala', 15.5000, -90.2500, 0),
array(136, 'VE', 'Venezuela', 8.0000, -66.0000, 0),
array(137, 'MN', 'Mongolia', 46.0000, 105.0000, 0),
array(138, 'TT', 'Trinidad and Tobago', 11.0000, -61.0000, 0),
array(139, 'AS', 'American Samoa', -14.3333, -170.0000, 0),
array(140, 'PA', 'Panama', 9.0000, -80.0000, 0),
array(141, 'AE', 'United Arab Emirates', 24.0000, 54.0000, 0),
array(142, 'MH', 'Marshall Islands', 9.0000, 168.0000, 0),
array(143, 'BH', 'Bahrain', 26.0000, 50.5500, 0),
array(144, 'CK', 'Cook Islands', -21.2333, -159.7667, 0),
array(145, 'GI', 'Gibraltar', 36.1833, -5.3667, 0),
array(146, 'PY', 'Paraguay', -23.0000, -58.0000, 0),
array(147, 'AG', 'Antigua and Barbuda', 17.0500, -61.8000, 0),
array(148, 'KN', 'Saint Kitts and Nevis', 17.3333, -62.7500, 0),
array(149, 'GL', 'Greenland', 72.0000, -40.0000, 0),
array(150, 'PW', 'Palau', 7.5000, 134.5000, 0),
array(151, 'SR', 'Suriname', 4.0000, -56.0000, 0),
array(152, 'BB', 'Barbados', 13.1667, -59.5333, 0),
array(153, 'GY', 'Guyana', 5.0000, -59.0000, 0),
array(154, 'PF', 'French Polynesia', -15.0000, -140.0000, 0),
array(155, 'MR', 'Mauritania', 20.0000, -12.0000, 0),
array(156, 'ML', 'Mali', 17.0000, -4.0000, 0),
array(157, 'CG', 'Congo', -1.0000, 15.0000, 0),
array(158, 'SC', 'Seychelles', -4.5833, 55.6667, 0),
array(159, 'LK', 'Sri Lanka', 7.0000, 81.0000, 0),
array(160, 'BW', 'Botswana', -22.0000, 24.0000, 0),
array(161, 'UZ', 'Uzbekistan', 41.0000, 64.0000, 0),
array(162, 'MO', 'Macau', 22.1667, 113.5500, 0),
array(163, 'QA', 'Qatar', 25.5000, 51.2500, 0),
array(164, 'WF', 'Wallis and Futuna', -13.3000, -176.2000, 0),
array(165, 'NA', 'Namibia', -22.0000, 17.0000, 0),
array(166, 'KH', 'Cambodia', 13.0000, 105.0000, 0),
array(167, 'BA', 'Bosnia and Herzegovina', 44.0000, 18.0000, 0),
array(168, 'CU', 'Cuba', 21.5000, -80.0000, 0),
array(169, 'UM', 'United States Minor Outlying Islands', 19.2833, 166.6000, 0),
array(170, 'HM', 'Heard Island and McDonald Islands', -53.1000, 72.5167, 0),
array(171, 'VN', 'Vietnam', 16.0000, 106.0000, 0),
array(172, 'NE', 'Niger', 16.0000, 8.0000, 0),
array(173, 'VU', 'Vanuatu', -16.0000, 167.0000, 0),
array(174, 'TC', 'Turks and Caicos Islands', 21.7500, -71.5833, 0),
array(175, 'UY', 'Uruguay', -33.0000, -56.0000, 0),
array(176, 'NC', 'New Caledonia', -21.5000, 165.5000, 0),
array(177, 'MP', 'Northern Mariana Islands', 15.2000, 145.7500, 0),
array(178, 'AI', 'Anguilla', 18.2500, -63.1667, 0),
array(179, 'GD', 'Grenada', 12.1167, -61.6667, 0),
array(180, 'MS', 'Montserrat', 16.7500, -62.2000, 0),
array(181, 'GP', 'Guadeloupe', 16.2500, -61.5833, 0),
array(182, 'MU', 'Mauritius', -20.2833, 57.5500, 0),
array(183, 'TD', 'Chad', 15.0000, 19.0000, 0),
array(184, 'LI', 'Liechtenstein', 47.1667, 9.5333, 0),
array(185, 'RE', 'Reunion', -21.1000, 55.6000, 0),
array(186, 'FO', 'Faroe Islands', 62.0000, -7.0000, 0),
array(187, 'SO', 'Somalia', 10.0000, 49.0000, 0),
array(188, 'GM', 'Gambia', 13.4667, -16.5667, 0),
array(189, 'GA', 'Gabon', -1.0000, 11.7500, 0),
array(190, 'KM', 'Comoros', -12.1667, 44.2500, 0),
array(191, 'LA', 'Lao People\'s Democratic Republic', 18.0000, 105.0000, 0),
array(192, 'BI', 'Burundi', -3.5000, 30.0000, 0),
array(193, 'TN', 'Tunisia', 34.0000, 9.0000, 0),
array(194, 'MQ', 'Martinique', 14.6667, -61.0000, 0),
array(195, 'KG', 'Kyrgyzstan', 41.0000, 75.0000, 0),
array(196, 'VA', 'Holy See (Vatican City State)', 41.9000, 12.4500, 0),
array(197, 'PS', 'Palestinian Territory', 32.0000, 35.2500, 0),
array(198, 'ET', 'Ethiopia', 8.0000, 38.0000, 0),
array(199, 'AD', 'Andorra', 42.5000, 1.5000, 0),
array(200, 'AQ', 'Antarctica', -90.0000, 0.0000, 0),
array(201, 'FJ', 'Fiji', -18.0000, 175.0000, 0),
array(202, 'BF', 'Burkina Faso', 13.0000, -2.0000, 0),
array(203, 'PG', 'Papua New Guinea', -6.0000, 147.0000, 0),
array(204, 'SD', 'Sudan', 15.0000, 30.0000, 0),
array(205, 'CF', 'Central African Republic', 7.0000, 21.0000, 0),
array(206, 'DJ', 'Djibouti', 11.5000, 43.0000, 0),
array(207, 'GQ', 'Equatorial Guinea', 2.0000, 10.0000, 0),
array(208, 'TM', 'Turkmenistan', 40.0000, 60.0000, 0),
array(209, 'CV', 'Cape Verde', 16.0000, -24.0000, 0),
array(210, 'ST', 'Sao Tome and Principe', 1.0000, 7.0000, 0),
array(211, 'FK', 'Falkland Islands', -51.7500, -59.0000, 0),
array(212, 'VC', 'Saint Vincent and the Grenadines', 13.2500, -61.2000, 0),
array(213, 'LS', 'Lesotho', -29.5000, 28.5000, 0),
array(214, 'BT', 'Bhutan', 27.5000, 90.5000, 0),
array(215, 'ER', 'Eritrea', 15.0000, 39.0000, 0),
array(216, 'AW', 'Aruba', 12.5000, -69.9667, 0),
array(217, 'SB', 'Solomon Islands', -8.0000, 159.0000, 0),
array(218, 'MV', 'Maldives', 3.2500, 73.0000, 0),
array(219, 'TV', 'Tuvalu', -8.0000, 178.0000, 0),
array(220, 'WS', 'Samoa', -13.5833, -172.3333, 0),
array(221, 'KI', 'Kiribati', 1.4167, 173.0000, 0),
array(222, 'IO', 'British Indian Ocean Territory', -6.0000, 71.5000, 0),
array(223, 'NP', 'Nepal', 28.0000, 84.0000, 0),
array(224, 'TO', 'Tonga', -20.0000, -175.0000, 0),
array(225, 'NR', 'Nauru', -0.5333, 166.9167, 0),
array(226, 'TK', 'Tokelau', -9.0000, -172.0000, 0),
array(227, 'NF', 'Norfolk Island', -29.0333, 167.9500, 0),
array(228, 'MM', 'Myanmar', 22.0000, 98.0000, 0),
array(229, 'KY', 'Cayman Islands', 19.5000, -80.5000, 0),
array(230, 'KP', 'North Korea', 40.0000, 127.0000, 0),
array(231, 'GF', 'French Guiana', 4.0000, -53.0000, 0),
array(232, 'YT', 'Mayotte', -12.8333, 45.1667, 0),
array(233, 'Z0', 'Reserved Addr', 0.0000, 0.0000, 0),
array(234, 'Z1', 'Loopback Addr', 0.0000, 0.0000, 0),
array(235, 'Z2', 'DHCP Address', 0.0000, 0.0000, 0),
array(236, 'Z3', 'Private Addr', 0.0000, 0.0000, 0),
array(241, 'CX', 'Christmas Island', -10.5000, 105.6667, 0),
array(242, 'EH', 'Western Sahara', 24.5000, -13.0000, 0),
array(243, 'GS', 'South Georgia', -54.5000, -37.0000, 0),
array(244, 'NU', 'Niue', -19.0333, -169.8667, 0),
array(245, 'PM', 'Saint Pierre and Miquelon', 46.8333, -56.3333, 0),
array(246, 'SH', 'Saint Helena', -15.9333, -5.7000, 0),
array(247, 'SJ', 'Svalbard', 78.0000, 20.0000, 0));
            foreach ($geoccitems as $geoccitem) {
                list($ci,$cc,$cn,$lat,$lon,$users) = $geoccitem;
                $query = "INSERT INTO $GeoccTable
                        (ci, cc, cn, lat, lon, users)
                        VALUES (?,?,?,?,?,?)";
                $bindvars = array((int)$ci, (string)$cc, (string)$cn, $lat, $lon, (int)$users);
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