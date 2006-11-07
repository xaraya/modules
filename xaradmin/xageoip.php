<?php
function netquery_admin_xageoip()
{
    if (!xarSecurityCheck('EditRole')) return;
    if (!xarVarFetch('step', 'int:1:100', $step, '1', XARVAR_NOT_REQUIRED)) return;
    $data = array();
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    $data['step'] = $step;
    switch ($step) {
        case '1':
        default:
            $data['body'] = '<br /><br />Creating and populating new data tables replaces any existing geoip and related geocc data tables.';
            $data['body'] .= ' Unless it has been backed up, all data contained in both tables will be lost.';
            $data['body'] .= '<br /><br />Do you wish to proceed?:';
            $data['body'] .= ' [<a href="'.xarModURL('netquery', 'admin', 'xageoip', array('step' => 99)).'">Yes</a>]';
            $data['body'] .= ' [<a href="'.xarModURL('netquery', 'admin', 'config').'">No</a>]<br /><br />';
            return $data;
            break;
        case '99':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
            $taboptarray = array('REPLACE');
            $idxoptarray = array('UNIQUE');
            $GeoccTable = $xartable['netquery_geocc'];
            $GeoccFields = "
                ci                I1         AUTO        PRIMARY         UNSIGNED,
                cc                C(2)       NOTNULL     DEFAULT '',
                cn                C(50)      NOTNULL     DEFAULT '',
                lat               N(7.4)     NOTNULL     DEFAULT 0.0000,
                lon               N(7.4)     NOTNULL     DEFAULT 0.0000,
                users             I          NOTNULL     DEFAULT 0       UNSIGNED
            ";
            $result = $datadict->createTable($GeoccTable, $GeoccFields, $taboptarray);
            if (!$result) return;
            $GeoipTable = $xartable['netquery_geoip'];
            $GeoipFields = "
                ipstart           I8         NOTNULL     DEFAULT 0       UNSIGNED,
                ipend             I8         NOTNULL     DEFAULT 0       UNSIGNED,
                ci                I1         NOTNULL     DEFAULT 0       UNSIGNED
            ";
            $result = $datadict->createTable($GeoipTable, $GeoipFields, $taboptarray);
            if (!$result) return;
            $GeoccItems = array(
array(1, 'A0', 'Reserved Address', '0.0000', '0.0000', 0),
array(2, 'A1', 'Anonymous Proxy', '0.0000', '0.0000', 0),
array(3, 'A2', 'Satellite Provider', '0.0000', '0.0000', 0),
array(4, 'A3', 'Private Address', '0.0000', '0.0000', 0),
array(5, 'AC', 'Ascension Island', '-7.9500', '-14.3667', 0),
array(6, 'AD', 'Andorra', '42.5000', '1.5000', 0),
array(7, 'AE', 'United Arab Emirates', '24.0000', '54.0000', 0),
array(8, 'AF', 'Afghanistan', '33.0000', '65.0000', 0),
array(9, 'AG', 'Antigua and Barbuda', '17.0500', '-61.8000', 0),
array(10, 'AI', 'Anguilla', '18.2500', '-63.1667', 0),
array(11, 'AL', 'Albania', '41.0000', '20.0000', 0),
array(12, 'AM', 'Armenia', '40.0000', '45.0000', 0),
array(13, 'AN', 'Netherlands Antilles', '12.2500', '-68.7500', 0),
array(14, 'AO', 'Angola', '-12.5000', '18.5000', 0),
array(15, 'AP', 'Asia-Pacific Region', '35.0000', '105.0000', 0),
array(16, 'AQ', 'Antarctica', '-90.0000', '0.0000', 0),
array(17, 'AR', 'Argentina', '-34.0000', '-64.0000', 0),
array(18, 'AS', 'American Samoa', '-14.3333', '-170.0000', 0),
array(19, 'AT', 'Austria', '47.3333', '13.3333', 0),
array(20, 'AU', 'Australia', '-27.0000', '133.0000', 0),
array(21, 'AW', 'Aruba', '12.5000', '-69.9667', 0),
array(22, 'AZ', 'Azerbaijan', '40.5000', '47.5000', 0),
array(23, 'BA', 'Bosnia and Herzegovina', '44.0000', '18.0000', 0),
array(24, 'BB', 'Barbados', '13.1667', '-59.5333', 0),
array(25, 'BD', 'Bangladesh', '24.0000', '90.0000', 0),
array(26, 'BE', 'Belgium', '50.8333', '4.0000', 0),
array(27, 'BF', 'Burkina Faso', '13.0000', '-2.0000', 0),
array(28, 'BG', 'Bulgaria', '43.0000', '25.0000', 0),
array(29, 'BH', 'Bahrain', '26.0000', '50.5500', 0),
array(30, 'BI', 'Burundi', '-3.5000', '30.0000', 0),
array(31, 'BJ', 'Benin', '9.5000', '2.2500', 0),
array(32, 'BM', 'Bermuda', '32.3333', '-64.7500', 0),
array(33, 'BN', 'Brunei Darussalam', '4.5000', '114.6667', 0),
array(34, 'BO', 'Bolivia', '-17.0000', '-65.0000', 0),
array(35, 'BR', 'Brazil', '-10.0000', '-55.0000', 0),
array(36, 'BS', 'Bahamas', '24.2500', '-76.0000', 0),
array(37, 'BT', 'Bhutan', '27.5000', '90.5000', 0),
array(38, 'BV', 'Bouvet Island', '-54.4333', '3.4000', 0),
array(39, 'BW', 'Botswana', '-22.0000', '24.0000', 0),
array(40, 'BY', 'Belarus', '53.0000', '28.0000', 0),
array(41, 'BZ', 'Belize', '17.2500', '-88.7500', 0),
array(42, 'CA', 'Canada', '60.0000', '-95.0000', 0),
array(43, 'CC', 'Cocos (Keeling) Islands', '35.0000', '105.0000', 0),
array(44, 'CD', 'Congo', '0.0000', '25.0000', 0),
array(45, 'CF', 'Central African Republic', '7.0000', '21.0000', 0),
array(46, 'CG', 'Congo', '-1.0000', '15.0000', 0),
array(47, 'CH', 'Switzerland', '47.0000', '8.0000', 0),
array(48, 'CI', 'Cote D\'Ivoire', '8.0000', '-5.0000', 0),
array(49, 'CK', 'Cook Islands', '-21.2333', '-159.7667', 0),
array(50, 'CL', 'Chile', '-30.0000', '-71.0000', 0),
array(51, 'CM', 'Cameroon', '6.0000', '12.0000', 0),
array(52, 'CN', 'China', '35.0000', '105.0000', 0),
array(53, 'CO', 'Colombia', '4.0000', '-72.0000', 0),
array(54, 'CR', 'Costa Rica', '10.0000', '-84.0000', 0),
array(55, 'CS', 'Serbia and Montenegro', '44.0000', '21.0000', 0),
array(56, 'CU', 'Cuba', '21.5000', '-80.0000', 0),
array(57, 'CV', 'Cape Verde', '16.0000', '-24.0000', 0),
array(58, 'CX', 'Christmas Island', '-10.5000', '105.6667', 0),
array(59, 'CY', 'Cyprus', '35.0000', '33.0000', 0),
array(60, 'CZ', 'Czech Republic', '49.7500', '15.5000', 0),
array(61, 'DE', 'Germany', '51.0000', '9.0000', 0),
array(62, 'DJ', 'Djibouti', '11.5000', '43.0000', 0),
array(63, 'DK', 'Denmark', '56.0000', '10.0000', 0),
array(64, 'DM', 'Dominica', '15.4167', '-61.3333', 0),
array(65, 'DO', 'Dominican Republic', '19.0000', '-70.6667', 0),
array(66, 'DZ', 'Algeria', '28.0000', '3.0000', 0),
array(67, 'EC', 'Ecuador', '-2.0000', '-77.5000', 0),
array(68, 'EE', 'Estonia', '59.0000', '26.0000', 0),
array(69, 'EG', 'Egypt', '27.0000', '30.0000', 0),
array(70, 'EH', 'Western Sahara', '24.5000', '-13.0000', 0),
array(71, 'ER', 'Eritrea', '15.0000', '39.0000', 0),
array(72, 'ES', 'Spain', '40.0000', '-4.0000', 0),
array(73, 'ET', 'Ethiopia', '8.0000', '38.0000', 0),
array(74, 'EU', 'Europe', '47.0000', '8.0000', 0),
array(75, 'FI', 'Finland', '64.0000', '26.0000', 0),
array(76, 'FJ', 'Fiji', '-18.0000', '175.0000', 0),
array(77, 'FK', 'Falkland Islands', '-51.7500', '-59.0000', 0),
array(78, 'FM', 'Micronesia', '6.9167', '158.2500', 0),
array(79, 'FO', 'Faroe Islands', '62.0000', '-7.0000', 0),
array(80, 'FR', 'France', '46.0000', '2.0000', 0),
array(81, 'GA', 'Gabon', '-1.0000', '11.7500', 0),
array(82, 'GB', 'United Kingdom', '54.0000', '-2.0000', 0),
array(83, 'GD', 'Grenada', '12.1167', '-61.6667', 0),
array(84, 'GE', 'Georgia', '42.0000', '43.5000', 0),
array(85, 'GF', 'French Guiana', '4.0000', '-53.0000', 0),
array(86, 'GG', 'Guernsey', '49.4603', '-2.5270', 0),
array(87, 'GH', 'Ghana', '8.0000', '-2.0000', 0),
array(88, 'GI', 'Gibraltar', '36.1833', '-5.3667', 0),
array(89, 'GL', 'Greenland', '72.0000', '-40.0000', 0),
array(90, 'GM', 'Gambia', '13.4667', '-16.5667', 0),
array(91, 'GN', 'Guinea', '11.0000', '-10.0000', 0),
array(92, 'GP', 'Guadeloupe', '16.2500', '-61.5833', 0),
array(93, 'GQ', 'Equatorial Guinea', '2.0000', '10.0000', 0),
array(94, 'GR', 'Greece', '39.0000', '22.0000', 0),
array(95, 'GS', 'South Georgia', '-54.5000', '-37.0000', 0),
array(96, 'GT', 'Guatemala', '15.5000', '-90.2500', 0),
array(97, 'GU', 'Guam', '13.4667', '144.7833', 0),
array(98, 'GW', 'Guinea-Bissau', '12.0000', '-15.0000', 0),
array(99, 'GY', 'Guyana', '5.0000', '-59.0000', 0),
array(100, 'HK', 'Hong Kong', '22.2500', '114.1667', 0),
array(101, 'HM', 'Heard Island and McDonald Islands', '-53.1000', '72.5167', 0),
array(102, 'HN', 'Honduras', '15.0000', '-86.5000', 0),
array(103, 'HR', 'Croatia', '45.1667', '15.5000', 0),
array(104, 'HT', 'Haiti', '19.0000', '-72.4167', 0),
array(105, 'HU', 'Hungary', '47.0000', '20.0000', 0),
array(106, 'ID', 'Indonesia', '-5.0000', '120.0000', 0),
array(107, 'IE', 'Ireland', '53.0000', '-8.0000', 0),
array(108, 'IL', 'Israel', '31.5000', '34.7500', 0),
array(109, 'IM', 'Isle of Man', '54.2307', '-4.5697', 0),
array(110, 'IN', 'India', '20.0000', '77.0000', 0),
array(111, 'IO', 'British Indian Ocean Territory', '-6.0000', '71.5000', 0),
array(112, 'IQ', 'Iraq', '33.0000', '44.0000', 0),
array(113, 'IR', 'Iran', '32.0000', '53.0000', 0),
array(114, 'IS', 'Iceland', '65.0000', '-18.0000', 0),
array(115, 'IT', 'Italy', '42.8333', '12.8333', 0),
array(116, 'JE', 'Jersey', '49.1919', '-2.1071', 0),
array(117, 'JM', 'Jamaica', '18.2500', '-77.5000', 0),
array(118, 'JO', 'Jordan', '31.0000', '36.0000', 0),
array(119, 'JP', 'Japan', '36.0000', '138.0000', 0),
array(120, 'KE', 'Kenya', '1.0000', '38.0000', 0),
array(121, 'KG', 'Kyrgyzstan', '41.0000', '75.0000', 0),
array(122, 'KH', 'Cambodia', '13.0000', '105.0000', 0),
array(123, 'KI', 'Kiribati', '1.4167', '173.0000', 0),
array(124, 'KM', 'Comoros', '-12.1667', '44.2500', 0),
array(125, 'KN', 'Saint Kitts and Nevis', '17.3333', '-62.7500', 0),
array(126, 'KP', 'North Korea', '40.0000', '127.0000', 0),
array(127, 'KR', 'Korea', '37.0000', '127.5000', 0),
array(128, 'KW', 'Kuwait', '29.5000', '45.7500', 0),
array(129, 'KY', 'Cayman Islands', '19.5000', '-80.5000', 0),
array(130, 'KZ', 'Kazakstan', '48.0000', '68.0000', 0),
array(131, 'LA', 'Lao People\'s Democratic Republic', '18.0000', '105.0000', 0),
array(132, 'LB', 'Lebanon', '33.8333', '35.8333', 0),
array(133, 'LC', 'Saint Lucia', '13.8833', '-61.1333', 0),
array(134, 'LI', 'Liechtenstein', '47.1667', '9.5333', 0),
array(135, 'LK', 'Sri Lanka', '7.0000', '81.0000', 0),
array(136, 'LR', 'Liberia', '6.5000', '-9.5000', 0),
array(137, 'LS', 'Lesotho', '-29.5000', '28.5000', 0),
array(138, 'LT', 'Lithuania', '56.0000', '24.0000', 0),
array(139, 'LU', 'Luxembourg', '49.7500', '6.1667', 0),
array(140, 'LV', 'Latvia', '57.0000', '25.0000', 0),
array(141, 'LY', 'Libyan Arab Jamahiriya', '25.0000', '17.0000', 0),
array(142, 'MA', 'Morocco', '32.0000', '-5.0000', 0),
array(143, 'MC', 'Monaco', '43.7333', '7.4000', 0),
array(144, 'MD', 'Moldova', '47.0000', '29.0000', 0),
array(145, 'MG', 'Madagascar', '-20.0000', '47.0000', 0),
array(146, 'MH', 'Marshall Islands', '9.0000', '168.0000', 0),
array(147, 'MK', 'Macedonia', '41.8333', '22.0000', 0),
array(148, 'ML', 'Mali', '17.0000', '-4.0000', 0),
array(149, 'MM', 'Myanmar', '22.0000', '98.0000', 0),
array(150, 'MN', 'Mongolia', '46.0000', '105.0000', 0),
array(151, 'MO', 'Macau', '22.1667', '113.5500', 0),
array(152, 'MP', 'Northern Mariana Islands', '15.2000', '145.7500', 0),
array(153, 'MQ', 'Martinique', '14.6667', '-61.0000', 0),
array(154, 'MR', 'Mauritania', '20.0000', '-12.0000', 0),
array(155, 'MS', 'Montserrat', '16.7500', '-62.2000', 0),
array(156, 'MT', 'Malta', '35.8333', '14.5833', 0),
array(157, 'MU', 'Mauritius', '-20.2833', '57.5500', 0),
array(158, 'MV', 'Maldives', '3.2500', '73.0000', 0),
array(159, 'MW', 'Malawi', '-13.5000', '34.0000', 0),
array(160, 'MX', 'Mexico', '23.0000', '-102.0000', 0),
array(161, 'MY', 'Malaysia', '2.5000', '112.5000', 0),
array(162, 'MZ', 'Mozambique', '-18.2500', '35.0000', 0),
array(163, 'NA', 'Namibia', '-22.0000', '17.0000', 0),
array(164, 'NC', 'New Caledonia', '-21.5000', '165.5000', 0),
array(165, 'NE', 'Niger', '16.0000', '8.0000', 0),
array(166, 'NF', 'Norfolk Island', '-29.0333', '167.9500', 0),
array(167, 'NG', 'Nigeria', '10.0000', '8.0000', 0),
array(168, 'NI', 'Nicaragua', '13.0000', '-85.0000', 0),
array(169, 'NL', 'Netherlands', '52.5000', '5.7500', 0),
array(170, 'NO', 'Norway', '62.0000', '10.0000', 0),
array(171, 'NP', 'Nepal', '28.0000', '84.0000', 0),
array(172, 'NR', 'Nauru', '-0.5333', '166.9167', 0),
array(173, 'NU', 'Niue', '-19.0333', '-169.8667', 0),
array(174, 'NZ', 'New Zealand', '-41.0000', '174.0000', 0),
array(175, 'OM', 'Oman', '21.0000', '57.0000', 0),
array(176, 'PA', 'Panama', '9.0000', '-80.0000', 0),
array(177, 'PE', 'Peru', '-10.0000', '-76.0000', 0),
array(178, 'PF', 'French Polynesia', '-15.0000', '-140.0000', 0),
array(179, 'PG', 'Papua New Guinea', '-6.0000', '147.0000', 0),
array(180, 'PH', 'Philippines', '13.0000', '122.0000', 0),
array(181, 'PK', 'Pakistan', '30.0000', '70.0000', 0),
array(182, 'PL', 'Poland', '52.0000', '20.0000', 0),
array(183, 'PM', 'Saint Pierre and Miquelon', '46.8333', '-56.3333', 0),
array(184, 'PN', 'Pitcairn Islands', '25.0667', '-130.0833', 0),
array(185, 'PR', 'Puerto Rico', '18.2500', '-66.5000', 0),
array(186, 'PS', 'Palestinian Territory', '32.0000', '35.2500', 0),
array(187, 'PT', 'Portugal', '39.5000', '-8.0000', 0),
array(188, 'PW', 'Palau', '7.5000', '134.5000', 0),
array(189, 'PY', 'Paraguay', '-23.0000', '-58.0000', 0),
array(190, 'QA', 'Qatar', '25.5000', '51.2500', 0),
array(191, 'RE', 'Reunion', '-21.1000', '55.6000', 0),
array(192, 'RO', 'Romania', '46.0000', '25.0000', 0),
array(193, 'RU', 'Russia', '60.0000', '100.0000', 0),
array(194, 'RW', 'Rwanda', '-2.0000', '30.0000', 0),
array(195, 'SA', 'Saudi Arabia', '25.0000', '45.0000', 0),
array(196, 'SB', 'Solomon Islands', '-8.0000', '159.0000', 0),
array(197, 'SC', 'Seychelles', '-4.5833', '55.6667', 0),
array(198, 'SD', 'Sudan', '15.0000', '30.0000', 0),
array(199, 'SE', 'Sweden', '62.0000', '15.0000', 0),
array(200, 'SG', 'Singapore', '1.3667', '103.8000', 0),
array(201, 'SH', 'Saint Helena', '-15.9333', '-5.7000', 0),
array(202, 'SI', 'Slovenia', '46.0000', '15.0000', 0),
array(203, 'SJ', 'Svalbard', '78.0000', '20.0000', 0),
array(204, 'SK', 'Slovakia', '48.6667', '19.5000', 0),
array(205, 'SL', 'Sierra Leone', '8.5000', '-11.5000', 0),
array(206, 'SM', 'San Marino', '43.7667', '12.4167', 0),
array(207, 'SN', 'Senegal', '14.0000', '-14.0000', 0),
array(208, 'SO', 'Somalia', '10.0000', '49.0000', 0),
array(209, 'SR', 'Suriname', '4.0000', '-56.0000', 0),
array(210, 'ST', 'Sao Tome and Principe', '1.0000', '7.0000', 0),
array(211, 'SU', 'Russian Federation', '60.0000', '100.0000', 0),
array(212, 'SV', 'El Salvador', '13.8333', '-88.9167', 0),
array(213, 'SY', 'Syrian Arab Republic', '35.0000', '38.0000', 0),
array(214, 'SZ', 'Swaziland', '-26.5000', '31.5000', 0),
array(215, 'TC', 'Turks and Caicos Islands', '21.7500', '-71.5833', 0),
array(216, 'TD', 'Chad', '15.0000', '19.0000', 0),
array(217, 'TF', 'French Southern Territories', '-43.0000', '67.0000', 0),
array(218, 'TG', 'Togo', '8.0000', '1.1667', 0),
array(219, 'TH', 'Thailand', '15.0000', '100.0000', 0),
array(220, 'TJ', 'Tajikistan', '39.0000', '71.0000', 0),
array(221, 'TK', 'Tokelau', '-9.0000', '-172.0000', 0),
array(222, 'TL', 'Timor-Leste', '-8.8333', '125.7500', 0),
array(223, 'TM', 'Turkmenistan', '40.0000', '60.0000', 0),
array(224, 'TN', 'Tunisia', '34.0000', '9.0000', 0),
array(225, 'TO', 'Tonga', '-20.0000', '-175.0000', 0),
array(226, 'TR', 'Turkey', '39.0000', '35.0000', 0),
array(227, 'TT', 'Trinidad and Tobago', '11.0000', '-61.0000', 0),
array(228, 'TV', 'Tuvalu', '-8.0000', '178.0000', 0),
array(229, 'TW', 'Taiwan', '23.5000', '121.0000', 0),
array(230, 'TZ', 'Tanzania', '-6.0000', '35.0000', 0),
array(231, 'UA', 'Ukraine', '49.0000', '32.0000', 0),
array(232, 'UG', 'Uganda', '1.0000', '32.0000', 0),
array(233, 'UK', 'United Kingdom', '54.0000', '-2.0000', 0),
array(234, 'UM', 'United States Minor Outlying Islands', '19.2833', '166.6000', 0),
array(235, 'US', 'United States', '38.0000', '-97.0000', 0),
array(236, 'UY', 'Uruguay', '-33.0000', '-56.0000', 0),
array(237, 'UZ', 'Uzbekistan', '41.0000', '64.0000', 0),
array(238, 'VA', 'Holy See (Vatican City State)', '41.9000', '12.4500', 0),
array(239, 'VC', 'Saint Vincent and the Grenadines', '13.2500', '-61.2000', 0),
array(240, 'VE', 'Venezuela', '8.0000', '-66.0000', 0),
array(241, 'VG', 'British Virgin Islands', '18.5000', '-64.5000', 0),
array(242, 'VI', 'Virgin Islands', '18.3333', '-64.8333', 0),
array(243, 'VN', 'Vietnam', '16.0000', '106.0000', 0),
array(244, 'VU', 'Vanuatu', '-16.0000', '167.0000', 0),
array(245, 'WF', 'Wallis and Futuna', '-13.3000', '-176.2000', 0),
array(246, 'WS', 'Samoa', '-13.5833', '-172.3333', 0),
array(247, 'YE', 'Yemen', '15.0000', '48.0000', 0),
array(248, 'YT', 'Mayotte', '-12.8333', '45.1667', 0),
array(249, 'YU', 'Yugoslavia', '44.0000', '21.0000', 0),
array(250, 'ZA', 'South Africa', '-29.0000', '24.0000', 0),
array(251, 'ZM', 'Zambia', '-15.0000', '30.0000', 0),
array(252, 'ZW', 'Zimbabwe', '-20.0000', '30.0000', 0));
            foreach ($GeoccItems as $GeoccItem) {
                list($ci,$cc,$cn,$lat,$lon,$users) = $GeoccItem;
                $query = "INSERT INTO $GeoccTable
                        (ci, cc, cn, lat, lon, users)
                        VALUES (?,?,?,?,?,?)";
                $bindvars = array((int)$ci, (string)$cc, (string)$cn, $lat, $lon, (int)$users);
                $result =& $dbconn->Execute($query,$bindvars);
            }
            if ($dbconn->ErrorNo() != 0) return;
            $GeoipItems = array(
array(0, 16777215, 1),             # Reserved block 0/8
array(167772160, 184549375, 4),    # Private address block 10/8 (Class A)
array(2130706432, 2147483647, 4),  # Private address block 127/8 (Loopback)
array(2147483648, 2147549183, 1),  # Reserved block 128.0/16
array(2851995648, 2852061183, 4),  # Private address block 169.254/16 (Class B for DHCP)
array(2886729728, 2887778303, 4),  # Private address blocks 172.16/12 (Class B x16 contiguous)
array(3221159936, 3221225471, 1),  # Reserved block 191.255/16
array(3221225472, 3221225727, 1),  # Reserved block 192.0.0/24
array(3232235520, 3232301055, 4),  # Private address blocks 192.168/16 (Class C x256 contiguous
array(3758096128, 3758096383, 1)); # Reserved block 223.255.255/24
            foreach ($GeoipItems as $GeoipItem) {
                list($ipstart,$ipend,$ci) = $GeoipItem;
                $query = "INSERT INTO $GeoipTable
                        (ipstart, ipend, ci)
                        VALUES (?,?,?)";
                $bindvars = array($ipstart, $ipend, (int)$ci);
                $result =& $dbconn->Execute($query,$bindvars);
            }
            if ($dbconn->ErrorNo() != 0) return;
            xarResponseRedirect(xarModURL('netquery', 'admin', 'xageoip', array('step' => '2')));
            return true;
            break;
        case '2':
            $data['body'] = '<br /><br />New geoip and geocc tables have been created and geocc has been populated with initial entries.';
            $data['body'] .= '<br />Please click <a href="'.xarModURL('netquery', 'admin', 'xageoip2', array('step' => '2')).'">HERE</a> to continue populating the main geoip table.';
            $data['body'] .= '<br /><br />The process (adding 70,000+ new records) may take a few minutes. Please be patient.';
            return $data;
            break;
        case '3':
            $data['body'] = '<br /><br />Process completed. The new geoip and geocc tables have now been fully populated.';
            $data['body'] .= '<br /><br />Please click <a href="'.xarModURL('netquery', 'admin', 'config').'">HERE</a> to return to Netquery\'s main admin panel.<br /><br />';
            return $data;
            break;
    }
    return true;
}
?>