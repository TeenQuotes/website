google.load('visualization', '1',
{
	'packages': ['geochart', 'corechart', 'table'],
	'language': 'fr_FR'
});

function drawMapGeo()
{
	var data = google.visualization.arrayToDataTable([
		[pays_txt, visits_txt],
		['United States',821260],
		['Canada',90775],
		['United Kingdom',84395],
		['Unknow',63676],
		['Philippines',62668],
		['Malaysia',32011],
		['Australia',25083],
		['India',16719],
		['Singapore',16612],
		['Norway',11132],
		['France',10703],
		['Indonesia',10652],
		['Netherlands',9484],
		['Germany',9286],
		['Mexico',8108],
		['Sweden',7899],
		['Ireland',7254],
		['New Zealand',5707],
		['Belgium',4993],
		['United Arab Emirates',4217],
		['Saudi Arabia',3601],
		['South Africa',3349],
		['Egypt',3110],
		['Romania',2298],
		['Puerto Rico',2280],
		['Denmark',2211],
		['Thailand',2117],
		['Pakistan',1991],
		['Hong Kong',1983],
		['Jamaica',1856],
		['Malta',1840],
		['Poland',1709],
		['Japan',1666],
		['Austria',1558],
		['Guam',1556],
		['Spain',1523],
		['Lebanon',1428],
		['Portugal',1361],
		['Israel',1296],
		['Switzerland',1238],
		['Trinidad and Tobago',1226],
		['Finland',1224],
		['Italy',1148],
		['Brazil',1102],
		['Mauritius',1036],
		['Nigeria',988],
		['Morocco',930],
		['Greece',887],
		['Serbia',851],
		['Algeria',850],
		['Cyprus',821],
		['Bahamas',780],
		['Peru',773],
		['Albania',740],
		['Vietnam',725],
		['Qatar',720],
		['Colombia',710],
		['Dominican Republic',696],
		['Slovenia',682],
		['China',677],
		['Bulgaria',674],
		['Hungary',648],
		['Belize',637],
		['Kuwait',623],
		['Suriname',613],
		['Turkey',598],
		['Jordan',584],
		['Sri Lanka',579],
		['Lithuania',569],
		['Ecuador',557],
		['Croatia',546],
		['Nepal',539],
		['Panama',521],
		['Kenya',504],
		['U.S. Virgin Islands',500],
		['Honduras',484],
		['Guatemala',475],
		['Bangladesh',474],
		['Chile',463],
		['Brunei',444],
		['Argentina',433],
		['Bahrain',431],
		['El Salvador',431],
		['Venezuela',423],
		['Macedonia [FYROM]',403],
		['Ukraine',389],
		['Northern Mariana Islands',388],
		['Guyana',377],
		['Russia',356],
		['Tunisia',356],
		['Estonia',344],
		['Bosnia and Herzegovina',339],
		['Botswana',332],
		['Kosovo',323],
		['South Korea',310],
		['Aruba',309],
		['Macau',295],
		['Nicaragua',292],
		['Costa Rica',287],
		['Tanzania',276],
		['Barbados',272],
		['Czech Republic',261],
		['Cambodia',259],
		['Luxembourg',241],
		['Turks and Caicos Islands',231],
		['Oman',228],
		['Bermuda',227],
		['Maldives',226],
		['Iceland',220],
		['Slovakia',217],
		['Ghana',211],
		['Namibia',211],
		['Georgia',203],
		['Dominica',196],
		['Gambia',187],
		['Jersey',174],
		['Uruguay',173],
		['Montenegro',167],
		['Iraq',163],
		['Saint Vincent and the Grenadines',157],
		['Taiwan',156],
		['Bolivia',152],
		['Syria',152],
		['Saint Lucia',149],
		['Fiji',144],
		['Cayman Islands',140],
		['Iran',138],
		['Zimbabwe',129],
		['Latvia',126],
		['Netherlands Antilles',113],
		['Mozambique',111],
		['Ethiopia',104],
		['Côte d’Ivoire',98],
		['Senegal',95],
		['Uganda',87],
		['Paraguay',84],
		['Antigua and Barbuda',77],
		['British Virgin Islands',77],
		['Belarus',76],
		['Guernsey',76],
		['Haiti',74],
		['Isle of Man',74],
		['Myanmar [Burma]',73],
		['Malawi',69],
		['Mongolia',65],
		['Sudan',63],
		['Gibraltar',61],
		['Moldova',59],
		['Bhutan',58],
		['Marshall Islands',58],
		['Cameroon',55],
		['Seychelles',55],
		['Zambia',55],
		['Palestine',54],
		['Curaçao',52],
		['Armenia',51],
		['Azerbaijan',47],
		['Saint Kitts and Nevis',45],
		['Libya',40],
		['Rwanda',40],
		['Grenada',39],
		['Yemen',36],
		['Réunion',33],
		['Andorra',30],
		['Kazakhstan',28],
		['Micronesia',26],
		['French Polynesia',23],
		['Angola',22],
		['Sint Maarten',22],
		['Uzbekistan',21],
		['Åland Islands',20],
		['Monaco',20],
		['Madagascar',19],
		['Sierra Leone',19],
		['Anguilla',17],
		['Faroe Islands',15],
		['Guadeloupe',13],
		['Laos',12],
		['Martinique',11],
		['Mauritania',10],
		['Tonga',10],
		['Papua New Guinea',9],
		['Gabon',8],
		['Lesotho',7],
		['Timor-Leste',7],
		['Burkina Faso',6],
		['Congo [DRC]',6],
		['Cook Islands',6],
		['Palau',6],
		['Somalia',6],
		['Swaziland',6],
		['Burundi',5],
		['French Guiana',5],
		['Niger',5],
		['Norfolk Island',5],
		['Togo',5],
		['Cuba',4],
		['Mali',4],
		['Vanuatu',4],
		['Benin',3],
		['Cape Verde',3],
		['Djibouti',3],
		['Greenland',3],
		['Saint Martin',3],
		['New Caledonia',3],
		['Solomon Islands',3],
		['Turkmenistan',3],
		['American Samoa',2],
		['Tajikistan',2],
		['Tuvalu',2],
		['Caribbean Netherlands',1],
		['Kyrgyzstan',1],
		['Kiribati',1],
		['Liberia',1],
		['Saint Helena',1],
		['Samoa',1]
		]);

var options = {
	title: domain_visits,
	colorAxis:
	{
		minValue: 0,
		colors: ['#D1EDF8', '#098FC8']
	},
	legend:
	{
		numberFormat: '#,###'
	}
};

var formatter = new google.visualization.NumberFormat(
{
	groupingSymbol: ' ',
	fractionDigits: 0,
	suffix: ' ' + visits_txt.toLowerCase()
});
formatter.format(data, 1);

var chart = new google.visualization.GeoChart(document.getElementById('geoMap'));
chart.draw(data, options);
};

function drawPieGeo()
{
	var data = google.visualization.arrayToDataTable([
		[pays_txt, visits_txt],
		['United States',821260],
		['Canada',90775],
		['United Kingdom',84395],
		['Unknow',63676],
		['Philippines',62668],
		['Malaysia',32011],
		['Australia',25083],
		['India',16719],
		['Singapore',16612],
		['Norway',11132],
		['France',10703],
		['Indonesia',10652],
		['Netherlands',9484],
		['Germany',9286],
		['Mexico',8108],
		['Sweden',7899],
		['Ireland',7254],
		['New Zealand',5707],
		['Belgium',4993],
		['United Arab Emirates',4217],
		['Saudi Arabia',3601],
		['South Africa',3349],
		['Egypt',3110],
		['Romania',2298],
		['Puerto Rico',2280],
		['Denmark',2211],
		['Thailand',2117],
		['Pakistan',1991],
		['Hong Kong',1983],
		['Jamaica',1856],
		['Malta',1840],
		['Poland',1709],
		['Japan',1666],
		['Austria',1558],
		['Guam',1556],
		['Spain',1523],
		['Lebanon',1428],
		['Portugal',1361],
		['Israel',1296],
		['Switzerland',1238],
		['Trinidad and Tobago',1226],
		['Finland',1224],
		['Italy',1148],
		['Brazil',1102],
		['Mauritius',1036],
		['Nigeria',988],
		['Morocco',930],
		['Greece',887],
		['Serbia',851],
		['Algeria',850],
		['Cyprus',821],
		['Bahamas',780],
		['Peru',773],
		['Albania',740],
		['Vietnam',725],
		['Qatar',720],
		['Colombia',710],
		['Dominican Republic',696],
		['Slovenia',682],
		['China',677],
		['Bulgaria',674],
		['Hungary',648],
		['Belize',637],
		['Kuwait',623],
		['Suriname',613],
		['Turkey',598],
		['Jordan',584],
		['Sri Lanka',579],
		['Lithuania',569],
		['Ecuador',557],
		['Croatia',546],
		['Nepal',539],
		['Panama',521],
		['Kenya',504],
		['U.S. Virgin Islands',500],
		['Honduras',484],
		['Guatemala',475],
		['Bangladesh',474],
		['Chile',463],
		['Brunei',444],
		['Argentina',433],
		['Bahrain',431],
		['El Salvador',431],
		['Venezuela',423],
		['Macedonia [FYROM]',403],
		['Ukraine',389],
		['Northern Mariana Islands',388],
		['Guyana',377],
		['Russia',356],
		['Tunisia',356],
		['Estonia',344],
		['Bosnia and Herzegovina',339],
		['Botswana',332],
		['Kosovo',323],
		['South Korea',310],
		['Aruba',309],
		['Macau',295],
		['Nicaragua',292],
		['Costa Rica',287],
		['Tanzania',276],
		['Barbados',272],
		['Czech Republic',261],
		['Cambodia',259],
		['Luxembourg',241],
		['Turks and Caicos Islands',231],
		['Oman',228],
		['Bermuda',227],
		['Maldives',226],
		['Iceland',220],
		['Slovakia',217],
		['Ghana',211],
		['Namibia',211],
		['Georgia',203],
		['Dominica',196],
		['Gambia',187],
		['Jersey',174],
		['Uruguay',173],
		['Montenegro',167],
		['Iraq',163],
		['Saint Vincent and the Grenadines',157],
		['Taiwan',156],
		['Bolivia',152],
		['Syria',152],
		['Saint Lucia',149],
		['Fiji',144],
		['Cayman Islands',140],
		['Iran',138],
		['Zimbabwe',129],
		['Latvia',126],
		['Netherlands Antilles',113],
		['Mozambique',111],
		['Ethiopia',104],
		['Côte d’Ivoire',98],
		['Senegal',95],
		['Uganda',87],
		['Paraguay',84],
		['Antigua and Barbuda',77],
		['British Virgin Islands',77],
		['Belarus',76],
		['Guernsey',76],
		['Haiti',74],
		['Isle of Man',74],
		['Myanmar [Burma]',73],
		['Malawi',69],
		['Mongolia',65],
		['Sudan',63],
		['Gibraltar',61],
		['Moldova',59],
		['Bhutan',58],
		['Marshall Islands',58],
		['Cameroon',55],
		['Seychelles',55],
		['Zambia',55],
		['Palestine',54],
		['Curaçao',52],
		['Armenia',51],
		['Azerbaijan',47],
		['Saint Kitts and Nevis',45],
		['Libya',40],
		['Rwanda',40],
		['Grenada',39],
		['Yemen',36],
		['Réunion',33],
		['Andorra',30],
		['Kazakhstan',28],
		['Micronesia',26],
		['French Polynesia',23],
		['Angola',22],
		['Sint Maarten',22],
		['Uzbekistan',21],
		['Åland Islands',20],
		['Monaco',20],
		['Madagascar',19],
		['Sierra Leone',19],
		['Anguilla',17],
		['Faroe Islands',15],
		['Guadeloupe',13],
		['Laos',12],
		['Martinique',11],
		['Mauritania',10],
		['Tonga',10],
		['Papua New Guinea',9],
		['Gabon',8],
		['Lesotho',7],
		['Timor-Leste',7],
		['Burkina Faso',6],
		['Congo [DRC]',6],
		['Cook Islands',6],
		['Palau',6],
		['Somalia',6],
		['Swaziland',6],
		['Burundi',5],
		['French Guiana',5],
		['Niger',5],
		['Norfolk Island',5],
		['Togo',5],
		['Cuba',4],
		['Mali',4],
		['Vanuatu',4],
		['Benin',3],
		['Cape Verde',3],
		['Djibouti',3],
		['Greenland',3],
		['Saint Martin',3],
		['New Caledonia',3],
		['Solomon Islands',3],
		['Turkmenistan',3],
		['American Samoa',2],
		['Tajikistan',2],
		['Tuvalu',2],
		['Caribbean Netherlands',1],
		['Kyrgyzstan',1],
		['Kiribati',1],
		['Liberia',1],
		['Saint Helena',1],
		['Samoa',1]
		]);

var options = {
	title: domain_visits
};

var formatter = new google.visualization.NumberFormat(
{
	groupingSymbol: ' ',
	fractionDigits: 0,
	suffix: ' ' + visits_txt.toLowerCase()
});
formatter.format(data, 1);

var chart = new google.visualization.PieChart(document.getElementById('pieGeo'));
chart.draw(data, options);
}

function drawSexUsers()
{
	var data = google.visualization.arrayToDataTable([
		[sex_txt, number_txt],
		['Hommes', 2963],
		['Femmes', 33805],
		]);

	var options = {
		title: sex_users,
		colors: ['#3366CC', '#EF7BB8']
	};

	var formatter = new google.visualization.NumberFormat(
	{
		groupingSymbol: ' ',
		fractionDigits: 0
	});
	formatter.format(data, 1);

	var chart = new google.visualization.PieChart(document.getElementById('sexUsers'));
	chart.draw(data, options);
}

function drawVisitsTechnology()
{
	var data = google.visualization.arrayToDataTable([
		[period, mobile_txt, desktop_txt, appios_txt, tablet_txt],
		['05/2011', 684, 1524, 0, 0],
		['06/2011', 6148, 13297, 0, 0],
		['07/2011', 9020, 18356, 0, 0],
		['08/2011', 8894, 19139, 0, 0],
		['09/2011', 8910, 19477, 0, 0],
		['10/2011', 10982, 24155, 0, 0],
		['11/2011', 12620, 24801, 0, 47],
		['12/2011', 17899, 26672, 0, 47],
		['01/2012', 22021, 31751, 0, 136],
		['02/2012', 23941, 31527, 0, 105],
		['03/2012', 31254, 37124, 0, 166],
		['04/2012', 33461, 43155, 0, 402],
		['05/2012', 31370, 40160, 0, 329],
		['06/2012', 32033, 38536, 0, 371],
		['07/2012', 33063, 38621, 0, 248],
		['08/2012', 29577, 34213, 0, 250],
		['09/2012', 25548, 29311, 0, 257],
		['10/2012', 26186, 28440, 0, 539],
		['11/2012', 20886, 25522, 20531, 588],
		['12/2012', 23552, 25017, 55345, 1134],
		['01/2013', 23894, 23387, 57977, 2684],
		['02/2013', 19808, 19043, 45693, 2324],
		['03/2013', 23685, 21615, 44810, 2883],
		['04/2013', 19648, 17892, 39975, 2509],
		['05/2013', 20373, 16617, 35953, 2512],
		['06/2013', 19965, 13432, 31746, 2296],
		['07/2013', 19072, 13193, 28597, 2372],
		['08/2013', 18995, 12463, 24139, 2340],
		['09/2013', 19016, 11618, 20773, 2243],
		['10/2013', 20568, 12625, 19825, 2670],
		['11/2013', 20166, 12097, 17926, 2989],
		['12/2013', 20731, 10778, 19277, 2883],
		]);

	var options = {
		title: users_technology
	};

	var formatter = new google.visualization.NumberFormat(
	{
		groupingSymbol: ' ',
		fractionDigits: 0,
		suffix: ' ' + visits_txt.toLowerCase()
	});
	formatter.format(data, 1);
	formatter.format(data, 2);
	formatter.format(data, 3);

	var chart = new google.visualization.LineChart(document.getElementById('drawVisitsTechnology'));
	chart.draw(data, options);
}

function drawVisitsDuration()
{
	var data = new google.visualization.DataTable();
	data.addColumn('string', months_txt);
	data.addColumn('datetime', duration_txt);
	data.addRows([
		['05/2011', new Date(0, 0, 0, 0, 5, 48)],
		['06/2011', new Date(0, 0, 0, 0, 5, 19)],
		['07/2011', new Date(0, 0, 0, 0, 4, 54)],
		['08/2011', new Date(0, 0, 0, 0, 5, 16)],
		['09/2011', new Date(0, 0, 0, 0, 5, 05)],
		['10/2011', new Date(0, 0, 0, 0, 5, 42)],
		['11/2011', new Date(0, 0, 0, 0, 5, 55)],
		['12/2011', new Date(0, 0, 0, 0, 5, 00)],
		['01/2012', new Date(0, 0, 0, 0, 5, 03)],
		['02/2012', new Date(0, 0, 0, 0, 6, 00)],
		['03/2012', new Date(0, 0, 0, 0, 6, 03)],
		['04/2012', new Date(0, 0, 0, 0, 6, 02)],
		['05/2012', new Date(0, 0, 0, 0, 5, 58)],
		['06/2012', new Date(0, 0, 0, 0, 5, 50)],
		['07/2012', new Date(0, 0, 0, 0, 5, 38)],
		['08/2012', new Date(0, 0, 0, 0, 5, 10)],
		['09/2012', new Date(0, 0, 0, 0, 5, 04)],
		['10/2012', new Date(0, 0, 0, 0, 4, 59)],
		['11/2012', new Date(0, 0, 0, 0, 4, 22)],
		['12/2012', new Date(0, 0, 0, 0, 4, 18)],
		['01/2013', new Date(0, 0, 0, 0, 4, 22)],
		['02/2013', new Date(0, 0, 0, 0, 4, 18)],
		['03/2013', new Date(0, 0, 0, 0, 4, 26)],
		['04/2013', new Date(0, 0, 0, 0, 4, 58)],
		['05/2013', new Date(0, 0, 0, 0, 5, 40)],
		['06/2013', new Date(0, 0, 0, 0, 4, 20)],
		['07/2013', new Date(0, 0, 0, 0, 4, 30)],
		['08/2013', new Date(0, 0, 0, 0, 4, 50)],
		['09/2013', new Date(0, 0, 0, 0, 4, 40)],
		['10/2013', new Date(0, 0, 0, 0, 4, 21)],
		['11/2013', new Date(0, 0, 0, 0, 4, 23)],
		['12/2013', new Date(0, 0, 0, 0, 4, 44)],
		]);

var options = {
	title: visits_duration
};

var formatter = new google.visualization.DateFormat(
{
	pattern: 'mm:ss'
});
formatter.format(data, 1);

var chart = new google.visualization.LineChart(document.getElementById('drawVisitsDuration'));
chart.draw(data, options);
}

function drawMobileOS()
{
	var data = google.visualization.arrayToDataTable([
		[os_txt, visits_txt],
		['iOS', 181669],
		['Android', 168923],
		['iPhone', 68737],
		['iPod', 54705],
		['BlackBerry', 52965],
		['Samsung', 2517],
		['Windows Phone', 1915],
		['(not set)', 1079],
		['Nokia', 507],
		['SymbianOS', 481],
		['Windows', 427],
		['iPad', 359],
		['LG', 195],
		['Series40', 129],
		['Nintendo 3DS', 70],
		['Sony', 64],
		['Firefox OS', 50],
		['MOT', 38],
		['LGE', 34],
		['Danger Hiptop', 23],
		['Bada', 8],
		['PalmOS', 3],
		['Playstation Vita', 3],
		]);

	var options = {
		title: os_mobile_txt
	};

	var formatter = new google.visualization.NumberFormat(
	{
		groupingSymbol: ' ',
		fractionDigits: 0,
		suffix: ' ' + visits_txt.toLowerCase()
	});
	formatter.format(data, 1);

	var chart = new google.visualization.PieChart(document.getElementById('drawMobileOS'));
	chart.draw(data, options);
}

google.setOnLoadCallback(drawMapGeo);
google.setOnLoadCallback(drawPieGeo);
google.setOnLoadCallback(drawSexUsers);
google.setOnLoadCallback(drawVisitsTechnology);
google.setOnLoadCallback(drawMobileOS);
google.setOnLoadCallback(drawVisitsDuration);