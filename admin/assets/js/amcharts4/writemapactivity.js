/*
    although ammap has methos like getAreaCenterLatitude and getAreaCenterLongitude,
    they are not suitable in quite a lot of cases as the center of some countries
    is even outside the country itself (like US, because of Alaska and Hawaii)
    That's why wehave the coordinates stored here
*/

var Country = {};
Country["AD"] = {
	"nameEN": "Andorra",
	"nameAR": "Ø£Ù†Ø¯ÙˆØ±Ø§",
	"latitude": 42.5,
    "longitude": 1.5
};
Country["AE"] = {
	"nameEN": "United Arab Emirates",
	"nameAR": "Ø§Ù„Ø¥Ù…Ø§Ø±Ø§Øª Ø§Ù„Ø¹Ø±Ø¨ÙŠØ© Ø§Ù„Ù…ØªØ­Ø¯Ø©",
	"latitude": 24,
    "longitude": 54
};
Country["AF"] = {
	"nameEN": "Afghanistan",
	"nameAR": "Ø£ÙØºØ§Ù†Ø³ØªØ§Ù†",
	"latitude": 33,
    "longitude": 65
};
Country["AG"] = {
	"nameEN": "Antigua and Barbuda",
	"nameAR": "Ø§Ù†ØªÙŠØºØ§ ÙˆØ¨Ø§Ø±Ø¨ÙˆØ¯Ø§",
	"latitude": 17.05,
    "longitude": -61.8
};
Country["AI"] = {
	"nameEN": "Anguilla",
	"nameAR": "Ø£Ù†ØºÙˆÙŠÙ„Ø§",
	"latitude": 18.25,
    "longitude": -63.1667
};
Country["AL"] = {
	"nameEN": "Albania",
	"nameAR": "Ø£Ù„Ø¨Ø§Ù†ÙŠØ§",
	"latitude": 41,
    "longitude": 20
};
Country["AM"] = {
	"nameEN": "Armenia",
	"nameAR": "Ø£Ø±Ù…ÙŠÙ†ÙŠØ§",
	"latitude": 40,
    "longitude": 45
};
Country["AN"] = {
	"nameEN": "Netherlands Antilles",
	"nameAR": "Ø¬Ø²Ø± Ø§Ù„Ø£Ù†ØªÙŠÙ„ Ø§Ù„Ù‡ÙˆÙ„Ù†Ø¯ÙŠØ©",
	"latitude": 12.25,
    "longitude": -68.75
};
Country["AO"] = {
	"nameEN": "Angola",
	"nameAR": "Ø£Ù†ØºÙˆÙ„Ø§",
	"latitude": -12.5,
    "longitude": 18.5
};
Country["AP"] = {
	"nameEN": "Asia/Pacific Region",
	"nameAR": "Ø¢Ø³ÙŠØ§ ÙˆØ§Ù„Ù…Ø­ÙŠØ· Ø§Ù„Ù‡Ø§Ø¯Ø¦",
	"latitude": 35,
    "longitude": 105
};
Country["AQ"] = {
	"nameEN": "Antarctica",
	"nameAR": "Ø§Ù„Ù‚Ø§Ø±Ø© Ø§Ù„Ù‚Ø·Ø¨ÙŠØ© Ø§Ù„Ø¬Ù†ÙˆØ¨ÙŠØ©",
	"latitude": -90,
    "longitude": 0
};
Country["AR"] = {
	"nameEN": "Argentina",
	"nameAR": "Ø§Ù„Ø£Ø±Ø¬Ù†ØªÙŠÙ†",
	"latitude": -34,
    "longitude": -64
};
Country["AS"] = {
	"nameEN": "American Samoa",
	"nameAR": "Ø³Ø§Ù…ÙˆØ§ Ø§Ù„Ø£Ù…Ø±ÙŠÙƒÙŠØ©",
	"latitude": -14.3333,
    "longitude": -170
};
Country["AT"] = {
	"nameEN": "Austria",
	"nameAR": "Ø§Ù„Ù†Ù…Ø³Ø§",
	"latitude": 47.3333,
    "longitude": 13.3333
};
Country["AU"] = {
	"nameEN": "Australia",
	"nameAR": "Ø£Ø³ØªØ±Ø§Ù„ÙŠØ§",
	"latitude": -27,
    "longitude": 133
};
Country["AW"] = {
	"nameEN": "Aruba",
	"nameAR": "Ø£Ø±ÙˆØ¨Ø§",
	"latitude": 12.5,
    "longitude": -69.9667
};
Country["AZ"] = {
	"nameEN": "Azerbaijan",
	"nameAR": "Ø£Ø°Ø±Ø¨ÙŠØ¬Ø§Ù†",
	"latitude": 40.5,
    "longitude": 47.5
};
Country["BA"] = {
	"nameEN": "Bosnia and Herzegovina",
	"nameAR": "Ø§Ù„Ø¨ÙˆØ³Ù†Ø© ÙˆØ§Ù„Ù‡Ø±Ø³Ùƒ",
	"latitude": 44,
    "longitude": 18
};
Country["BB"] = {
	"nameEN": "Barbados",
	"nameAR": "Ø¨Ø§Ø±Ø¨Ø§Ø¯ÙˆØ³",
	"latitude": 13.1667,
    "longitude": -59.5333
};
Country["BD"] = {
	"nameEN": "Bangladesh",
	"nameAR": "Ø¨Ù†ØºÙ„Ø§Ø¯ÙŠØ´",
	"latitude": 24,
    "longitude": 90
};
Country["BE"] = {
	"nameEN": "Belgium",
	"nameAR": "Ø¨Ù„Ø¬ÙŠÙƒØ§",
	"latitude": 50.8333,
    "longitude": 4
};
Country["BF"] = {
	"nameEN": "Burkina Faso",
	"nameAR": "Ø¨ÙˆØ±ÙƒÙŠÙ†Ø§ ÙØ§Ø³Ùˆ",
	"latitude": 13,
    "longitude": -2
};
Country["BG"] = {
	"nameEN": "Bulgaria",
	"nameAR": "Ø¨Ù„ØºØ§Ø±ÙŠØ§",
	"latitude": 43,
    "longitude": 25
};
Country["BH"] = {
	"nameEN": "Bahrain",
	"nameAR": "Ø§Ù„Ø¨Ø­Ø±ÙŠÙ†",
	"latitude": 26,
    "longitude": 50.55
};
Country["BI"] = {
	"nameEN": "Burundi",
	"nameAR": "Ø¨ÙˆØ±ÙˆÙ†Ø¯ÙŠ",
	"latitude": -3.5,
    "longitude": 30
};
Country["BJ"] = {
	"nameEN": "Benin",
	"nameAR": "Ø¨Ù†ÙŠÙ†",
	"latitude": 9.5,
    "longitude": 2.25
};
Country["BM"] = {
	"nameEN": "Bermuda",
	"nameAR": "Ø¨Ø±Ù…ÙˆØ¯Ø§",
	"latitude": 32.3333,
    "longitude": -64.75
};
Country["BN"] = {
	"nameEN": "Brunei Darussalam",
	"nameAR": "Ø¨Ø±ÙˆÙ†Ø§ÙŠ Ø¯Ø§Ø± Ø§Ù„Ø³Ù„Ø§Ù…",
	"latitude": 4.5,
    "longitude": 114.6667
};
Country["BO"] = {
	"nameEN": "Bolivia",
	"nameAR": "Ø¨ÙˆÙ„ÙŠÙÙŠØ§",
	"latitude": -17,
    "longitude": -65
};
Country["BR"] = {
	"nameEN": "Brazil",
	"nameAR": "Ø§Ù„Ø¨Ø±Ø§Ø²ÙŠÙ„",
	"latitude": -10,
    "longitude": -55
};
Country["BS"] = {
	"nameEN": "Bahamas",
	"nameAR": "Ø¨Ø§Ù‡Ø§Ù…Ø§Ø³",
	"latitude": 24.25,
    "longitude": -76
};
Country["BT"] = {
	"nameEN": "Bhutan",
	"nameAR": "Ø¨ÙˆØªØ§Ù†",
	"latitude": 27.5,
    "longitude": 90.5
};
Country["BV"] = {
	"nameEN": "Bouvet Island",
	"nameAR": "Ø¬Ø²ÙŠØ±Ø© Ø¨ÙˆÙÙŠØª",
	"latitude": -54.4333,
    "longitude": 3.4
};
Country["BW"] = {
	"nameEN": "Botswana",
	"nameAR": "Ø¨ÙˆØªØ³ÙˆØ§Ù†Ø§",
	"latitude": -22,
    "longitude": 24
};
Country["BY"] = {
	"nameEN": "Belarus",
	"nameAR": "",
	"latitude": 53,
    "longitude": 28
};
Country["BZ"] = {
	"nameEN": "Belize",
	"nameAR": "",
	"latitude": 17.25,
    "longitude": -88.75
};
Country["CA"] = {
	"nameEN": "Canada",
	"nameAR": "",
	"latitude": 54,
    "longitude": -100
};
Country["CC"] = {
	"nameEN": "Cocos (Keeling) Islands",
	"nameAR": "",
	"latitude": -12.5,
    "longitude": 96.8333
};
Country["CD"] = {
	"nameEN": "Congo, The Democratic Republic of the",
	"nameAR": "",
	"latitude": 0,
    "longitude": 25
};
Country["CF"] = {
	"nameEN": "Central African Republic",
	"nameAR": "",
	"latitude": 7,
    "longitude": 21
};
Country["CG"] = {
	"nameEN": "Congo",
	"nameAR": "",
	"latitude": -1,
    "longitude": 15
};
Country["CH"] = {
	"nameEN": "Switzerland",
	"nameAR": "",
	"latitude": 47,
    "longitude": 8
};
Country["CI"] = {
	"nameEN": "Cote D'Ivoire",
	"nameAR": "",
	"latitude": 8,
    "longitude": -5
};
Country["CK"] = {
	"nameEN": "Cook Islands",
	"nameAR": "",
	"latitude": -21.2333,
    "longitude": -159.7667
};
Country["CL"] = {
	"nameEN": "Chile",
	"nameAR": "",
	"latitude": -30,
    "longitude": -71
};
Country["CM"] = {
	"nameEN": "Cameroon",
	"nameAR": "",
	"latitude": 6,
    "longitude": 12
};
Country["CN"] = {
	"nameEN": "China",
	"nameAR": "",
	"latitude": 35,
    "longitude": 105
};
Country["CO"] = {
	"nameEN": "Colombia",
	"nameAR": "",
	"latitude": 4,
    "longitude": -72
};
Country["CR"] = {
	"nameEN": "Costa Rica",
	"nameAR": "",
	"latitude": 10,
    "longitude": -84
};
Country["CU"] = {
	"nameEN": "Cuba",
	"nameAR": "",
	"latitude": 21.5,
    "longitude": -80
};
Country["CV"] = {
	"nameEN": "Cape Verde",
	"nameAR": "",
	"latitude": 16,
    "longitude": -24
};
Country["CX"] = {
	"nameEN": "Christmas Island",
	"nameAR": "",
	"latitude": -10.5,
    "longitude": 105.6667
};
Country["CY"] = {
	"nameEN": "Cyprus",
	"nameAR": "",
	"latitude": 35,
    "longitude": 33
};
Country["CZ"] = {
	"nameEN": "Czech Republic",
	"nameAR": "",
	"latitude": 49.75,
    "longitude": 15.5
};
Country["DE"] = {
	"nameEN": "Germany",
	"nameAR": "",
	"latitude": 51,
    "longitude": 9
};
Country["DJ"] = {
	"nameEN": "Djibouti",
	"nameAR": "",
	"latitude": 11.5,
    "longitude": 43
};
Country["DK"] = {
	"nameEN": "Denmark",
	"nameAR": "",
	"latitude": 56,
    "longitude": 10
};
Country["DM"] = {
	"nameEN": "Dominica",
	"nameAR": "",
	"latitude": 15.4167,
    "longitude": -61.3333
};
Country["DO"] = {
	"nameEN": "Dominican Republic",
	"nameAR": "",
	"latitude": 19,
    "longitude": -70.6667
};
Country["DZ"] = {
	"nameEN": "Algeria",
	"nameAR": "Ø§Ù„Ø¬Ø²Ø§Ø¦Ø±",
	"latitude": 30,
    "longitude": 3
};
Country["EC"] = {
	"nameEN": "Ecuador",
	"nameAR": "",
	"latitude": -2,
    "longitude": -77.5
};
Country["EE"] = {
	"nameEN": "Estonia",
	"nameAR": "",
	"latitude": 59,
    "longitude": 26
};
Country["EG"] = {
	"nameEN": "Egypt",
	"nameAR": "Ù…ØµØ±",
	"latitude": 27,
    "longitude": 30
};
Country["EH"] = {
	"nameEN": "Western Sahara",
	"nameAR": "",
	"latitude": 24.5,
    "longitude": -13
};
Country["ER"] = {
	"nameEN": "Eritrea",
	"nameAR": "",
	"latitude": 15,
    "longitude": 39
};
Country["ES"] = {
	"nameEN": "Spain",
	"nameAR": "",
	"latitude": 40,
    "longitude": -4
};
Country["ET"] = {
	"nameEN": "Ethiopia",
	"nameAR": "",
	"latitude": 8,
    "longitude": 38
};
Country["EU"] = {
	"nameEN": "Europe",
	"nameAR": "",
	"latitude": 47,
    "longitude": 8
};
Country["FI"] = {
	"nameEN": "Finland",
	"nameAR": "",
	"latitude": 62,
    "longitude": 26
};
Country["FJ"] = {
	"nameEN": "Fiji",
	"nameAR": "",
	"latitude": -18,
    "longitude": 175
};
Country["FK"] = {
	"nameEN": "Falkland Islands (Malvinas)",
	"nameAR": "",
	"latitude": -51.75,
    "longitude": -59
};
Country["FM"] = {
	"nameEN": "Micronesia, Federated States of",
	"nameAR": "",
	"latitude": 6.9167,
    "longitude": 158.25
};
Country["FO"] = {
	"nameEN": "Faroe Islands",
	"nameAR": "",
	"latitude": 62,
    "longitude": -7
};
Country["FR"] = {
	"nameEN": "France",
	"nameAR": "",
	"latitude": 46,
    "longitude": 2
};
Country["GA"] = {
	"nameEN": "Gabon",
	"nameAR": "",
	"latitude": -1,
    "longitude": 11.75
};
Country["GB"] = {
	"nameEN": "United Kingdom",
	"nameAR": "",
	"latitude": 54,
    "longitude": -2
};
Country["GD"] = {
	"nameEN": "Grenada",
	"nameAR": "",
	"latitude": 12.1167,
    "longitude": -61.6667
};
Country["GE"] = {
	"nameEN": "Georgia",
	"nameAR": "",
	"latitude": 42,
    "longitude": 43.5
};
Country["GF"] = {
	"nameEN": "French Guiana",
	"nameAR": "",
	"latitude": 4,
    "longitude": -53
};
Country["GH"] = {
	"nameEN": "Ghana",
	"nameAR": "",
	"latitude": 8,
    "longitude": -2
};
Country["GI"] = {
	"nameEN": "Gibraltar",
	"nameAR": "",
	"latitude": 36.1833,
    "longitude": -5.3667
};
Country["GL"] = {
	"nameEN": "Greenland",
	"nameAR": "",
	"latitude": 72,
    "longitude": -40
};
Country["GM"] = {
	"nameEN": "Gambia",
	"nameAR": "",
	"latitude": 13.4667,
    "longitude": -16.5667
};
Country["GN"] = {
	"nameEN": "Guinea",
	"nameAR": "",
	"latitude": 11,
    "longitude": -10
};
Country["GP"] = {
	"nameEN": "Guadeloupe",
	"nameAR": "",
	"latitude": 16.25,
    "longitude": -61.5833
};
Country["GQ"] = {
	"nameEN": "Equatorial Guinea",
	"nameAR": "",
	"latitude": 2,
    "longitude": 10
};
Country["GR"] = {
	"nameEN": "Greece",
	"nameAR": "",
	"latitude": 39,
    "longitude": 22
};
Country["GS"] = {
	"nameEN": "South Georgia and the South Sandwich Islands",
	"nameAR": "",
	"latitude": -54.5,
    "longitude": -37
};
Country["GT"] = {
	"nameEN": "Guatemala",
	"nameAR": "",
	"latitude": 15.5,
    "longitude": -90.25
};
Country["GU"] = {
	"nameEN": "Guam",
	"nameAR": "",
	"latitude": 13.4667,
    "longitude": 144.7833
};
Country["GW"] = {
	"nameEN": "Guinea-Bissau",
	"nameAR": "",
	"latitude": 12,
    "longitude": -15
};
Country["GY"] = {
	"nameEN": "Guyana",
	"nameAR": "",
	"latitude": 5,
    "longitude": -59
};
Country["HK"] = {
	"nameEN": "Hong Kong",
	"nameAR": "",
	"latitude": 22.25,
    "longitude": 114.1667
};
Country["HM"] = {
	"nameEN": "Heard Island and McDonald Islands",
	"nameAR": "",
	"latitude": -53.1,
    "longitude": 72.5167
};
Country["HN"] = {
	"nameEN": "Honduras",
	"nameAR": "",
	"latitude": 15,
    "longitude": -86.5
};
Country["HR"] = {
	"nameEN": "Croatia",
	"nameAR": "",
	"latitude": 45.1667,
    "longitude": 15.5
};
Country["HT"] = {
	"nameEN": "Haiti",
	"nameAR": "",
	"latitude": 19,
    "longitude": -72.4167
};
Country["HU"] = {
	"nameEN": "Hungary",
	"nameAR": "",
	"latitude": 47,
    "longitude": 20
};
Country["ID"] = {
	"nameEN": "Indonesia",
	"nameAR": "",
	"latitude": -5,
    "longitude": 120
};
Country["IE"] = {
	"nameEN": "Ireland",
	"nameAR": "",
	"latitude": 53,
    "longitude": -8
};
Country["IL"] = {
	"nameEN": "Israel",
	"nameAR": "",
	"latitude": 31.5,
    "longitude": 34.75
};
Country["IN"] = {
	"nameEN": "India",
	"nameAR": "",
	"latitude": 20,
    "longitude": 77
};
Country["IO"] = {
	"nameEN": "British Indian Ocean Territory",
	"nameAR": "",
	"latitude": -6,
    "longitude": 71.5
};
Country["IQ"] = {
	"nameEN": "Iraq",
	"nameAR": "",
	"latitude": 33,
    "longitude": 44
};
Country["IR"] = {
	"nameEN": "Iran, Islamic Republic of",
	"nameAR": "",
	"latitude": 32,
    "longitude": 53
};
Country["IS"] = {
	"nameEN": "Iceland",
	"nameAR": "",
	"latitude": 65,
    "longitude": -18
};
Country["IT"] = {
	"nameEN": "Italy",
	"nameAR": "",
	"latitude": 42.8333,
    "longitude": 12.8333
};
Country["JM"] = {
	"nameEN": "Jamaica",
	"nameAR": "",
	"latitude": 18.25,
    "longitude": -77.5
};
Country["JO"] = {
	"nameEN": "Jordan",
	"nameAR": "",
	"latitude": 31,
    "longitude": 36
};
Country["JP"] = {
	"nameEN": "Japan",
	"nameAR": "",
	"latitude": 36,
    "longitude": 138
};
Country["KE"] = {
	"nameEN": "Kenya",
	"nameAR": "",
	"latitude": 1,
    "longitude": 38
};
Country["KG"] = {
	"nameEN": "Kyrgyzstan",
	"nameAR": "",
	"latitude": 41,
    "longitude": 75
};
Country["KH"] = {
	"nameEN": "Cambodia",
	"nameAR": "",
	"latitude": 13,
    "longitude": 105
};
Country["KI"] = {
	"nameEN": "Kiribati",
	"nameAR": "",
	"latitude": 1.4167,
    "longitude": 173
};
Country["KM"] = {
	"nameEN": "Comoros",
	"nameAR": "",
	"latitude": -12.1667,
    "longitude": 44.25
};
Country["KN"] = {
	"nameEN": "Saint Kitts and Nevis",
	"nameAR": "",
	"latitude": 17.3333,
    "longitude": -62.75
};
Country["KP"] = {
	"nameEN": "Korea, Democratic People's Republic of",
	"nameAR": "",
	"latitude": 40,
    "longitude": 127
};
Country["KR"] = {
	"nameEN": "Korea, Republic of",
	"nameAR": "",
	"latitude": 37,
    "longitude": 127.5
};
Country["KW"] = {
	"nameEN": "Kuwait",
	"nameAR": "Ø§Ù„ÙƒÙˆÙŠØª",
	"latitude": 29.3375,
    "longitude": 47.6581
};
Country["KY"] = {
	"nameEN": "Cayman Islands",
	"nameAR": "",
	"latitude": 19.5,
    "longitude": -80.5
};
Country["KZ"] = {
	"nameEN": "Kazakhstan",
	"nameAR": "",
	"latitude": 48,
    "longitude": 68
};
Country["LA"] = {
	"nameEN": "Lao People's Democratic Republic",
	"nameAR": "",
	"latitude": 18,
    "longitude": 105
};
Country["LB"] = {
	"nameEN": "Lebanon",
	"nameAR": "Ù„Ø¨Ù†Ø§Ù†",
	"latitude": 33.8333,
    "longitude": 35.8333
};
Country["LC"] = {
	"nameEN": "Saint Lucia",
	"nameAR": "",
	"latitude": 13.8833,
    "longitude": -61.1333
};
Country["LI"] = {
	"nameEN": "Liechtenstein",
	"nameAR": "",
	"latitude": 47.1667,
    "longitude": 9.5333
};
Country["LK"] = {
	"nameEN": "Sri Lanka",
	"nameAR": "",
	"latitude": 7,
    "longitude": 81
};
Country["LR"] = {
	"nameEN": "Liberia",
	"nameAR": "",
	"latitude": 6.5,
    "longitude": -9.5
};
Country["LS"] = {
	"nameEN": "Lesotho",
	"nameAR": "",
	"latitude": -29.5,
    "longitude": 28.5
};
Country["LT"] = {
	"nameEN": "Lithuania",
	"nameAR": "Ù„ÙŠØªÙˆØ§Ù†ÙŠØ§",
	"latitude": 55,
    "longitude": 24
};
Country["LU"] = {
	"nameEN": "Luxembourg",
	"nameAR": "",
	"latitude": 49.75,
    "longitude": 6
};
Country["LV"] = {
	"nameEN": "Latvia",
	"nameAR": "",
	"latitude": 57,
    "longitude": 25
};
Country["LY"] = {
	"nameEN": "Libya",
	"nameAR": "Ù„ÙŠØ¨ÙŠØ§",
	"latitude": 25,
    "longitude": 17
};
Country["MA"] = {
	"nameEN": "Morocco",
	"nameAR": "Ø§Ù„Ù…ØºØ±Ø¨",
	"latitude": 32,
    "longitude": -5
};
Country["MC"] = {
	"nameEN": "Monaco",
	"nameAR": "",
	"latitude": 43.7333,
    "longitude": 7.4
};
Country["MD"] = {
	"nameEN": "Moldova, Republic of",
	"nameAR": "",
	"latitude": 47,
    "longitude": 29
};
Country["ME"] = {
	"nameEN": "Montenegro",
	"nameAR": "",
	"latitude": 42.5,
    "longitude": 19.4
};
Country["MF"] = {
	"nameEN": "Saint Martin",
	"nameAR": "Ø³Ø§Ù† Ù…Ø§Ø±ØªÙŠÙ†",
	"latitude": 18.07,
    "longitude": -63.06
};
Country["MG"] = {
	"nameEN": "Madagascar",
	"nameAR": "",
	"latitude": -20,
    "longitude": 47
};
Country["MH"] = {
	"nameEN": "Marshall Islands",
	"nameAR": "",
	"latitude": 9,
    "longitude": 168
};
Country["MK"] = {
	"nameEN": "Macedonia",
	"nameAR": "",
	"latitude": 41.8333,
    "longitude": 22
};
Country["ML"] = {
	"nameEN": "Mali",
	"nameAR": "Ù…Ø§Ù„ÙŠ",
	"latitude": 17,
    "longitude": -4
};
Country["MM"] = {
	"nameEN": "Myanmar",
	"nameAR": "",
	"latitude": 22,
    "longitude": 98
};
Country["MN"] = {
	"nameEN": "Mongolia",
	"nameAR": "",
	"latitude": 46,
    "longitude": 105
};
Country["MO"] = {
	"nameEN": "Macau",
	"nameAR": "",
	"latitude": 22.1667,
    "longitude": 113.55
};
Country["MP"] = {
	"nameEN": "Northern Mariana Islands",
	"nameAR": "",
	"latitude": 15.2,
    "longitude": 145.75
};
Country["MQ"] = {
	"nameEN": "Martinique",
	"nameAR": "",
	"latitude": 14.6667,
    "longitude": -61
};
Country["MR"] = {
	"nameEN": "Mauritania",
	"nameAR": "Ù…ÙˆØ±ÙŠØªØ§Ù†ÙŠØ§",
	"latitude": 20,
    "longitude": -12
};
Country["MS"] = {
	"nameEN": "Montserrat",
	"nameAR": "",
	"latitude": 16.75,
    "longitude": -62.2
};
Country["MT"] = {
	"nameEN": "Malta",
	"nameAR": "Ù…Ø§Ù„Ø·Ø§",
	"latitude": 35.8333,
    "longitude": 14.5833
};
Country["MU"] = {
	"nameEN": "Mauritius",
	"nameAR": "",
	"latitude": -20.2833,
    "longitude": 57.55
};
Country["MV"] = {
	"nameEN": "Maldives",
	"nameAR": "",
	"latitude": 3.25,
    "longitude": 73
};
Country["MW"] = {
	"nameEN": "Malawi",
	"nameAR": "",
	"latitude": -13.5,
    "longitude": 34
};
Country["MX"] = {
	"nameEN": "Mexico",
	"nameAR": "",
	"latitude": 23,
    "longitude": -102
};
Country["MY"] = {
	"nameEN": "Malaysia",
	"nameAR": "",
	"latitude": 2.5,
    "longitude": 112.5
};
Country["MZ"] = {
	"nameEN": "Mozambique",
	"nameAR": "",
	"latitude": -18.25,
    "longitude": 35
};
Country["NA"] = {
	"nameEN": "Namibia",
	"nameAR": "",
	"latitude": -22,
    "longitude": 17
};
Country["NC"] = {
	"nameEN": "New Caledonia",
	"nameAR": "",
	"latitude": -21.5,
    "longitude": 165.5
};
Country["NE"] = {
	"nameEN": "Niger",
	"nameAR": "",
	"latitude": 16,
    "longitude": 8
};
Country["NF"] = {
	"nameEN": "Norfolk Island",
	"nameAR": "",
	"latitude": -29.0333,
    "longitude": 167.95
};
Country["NG"] = {
	"nameEN": "Nigeria",
	"nameAR": "",
	"latitude": 10,
    "longitude": 8
};
Country["NI"] = {
	"nameEN": "Nicaragua",
	"nameAR": "",
	"latitude": 13,
    "longitude": -85
};
Country["NL"] = {
	"nameEN": "Netherlands",
	"nameAR": "",
	"latitude": 52.5,
    "longitude": 5.75
};
Country["NO"] = {
	"nameEN": "Norway",
	"nameAR": "",
	"latitude": 62,
    "longitude": 10
};
Country["NP"] = {
	"nameEN": "Nepal",
	"nameAR": "",
	"latitude": 28,
    "longitude": 84
};
Country["NR"] = {
	"nameEN": "Nauru",
	"nameAR": "",
	"latitude": -0.5333,
    "longitude": 166.9167
};
Country["NU"] = {
	"nameEN": "Niue",
	"nameAR": "",
	"latitude": -19.0333,
    "longitude": -169.8667
};
Country["NZ"] = {
	"nameEN": "New Zealand",
	"nameAR": "",
	"latitude": -41,
    "longitude": 174
};
Country["OM"] = {
	"nameEN": "Oman",
	"nameAR": "Ø¹Ù…Ø§Ù†",
	"latitude": 21,
    "longitude": 57
};
Country["PA"] = {
	"nameEN": "Panama",
	"nameAR": "",
	"latitude": 9,
    "longitude": -80
};
Country["PE"] = {
	"nameEN": "Peru",
	"nameAR": "",
	"latitude": -10,
    "longitude": -76
};
Country["PF"] = {
	"nameEN": "French Polynesia",
	"nameAR": "",
	"latitude": -15,
    "longitude": -140
};
Country["PG"] = {
	"nameEN": "Papua New Guinea",
	"nameAR": "",
	"latitude": -6,
    "longitude": 147
};
Country["PH"] = {
	"nameEN": "Philippines",
	"nameAR": "",
	"latitude": 13,
    "longitude": 122
};
Country["PK"] = {
	"nameEN": "Pakistan",
	"nameAR": "Ø¨Ø§ÙƒØ³ØªØ§Ù†",
	"latitude": 30,
    "longitude": 70
};
Country["PL"] = {
	"nameEN": "Poland",
	"nameAR": "",
	"latitude": 52,
    "longitude": 20
};
Country["PM"] = {
	"nameEN": "Saint Pierre and Miquelon",
	"nameAR": "",
	"latitude": 46.8333,
    "longitude": -56.3333
};
Country["PR"] = {
	"nameEN": "Puerto Rico",
	"nameAR": "",
	"latitude": 18.25,
    "longitude": -66.5
};
Country["PS"] = {
	"nameEN": "Palestinian Territory",
	"nameAR": "ÙÙ„Ø³Ø·ÙŠÙ†",
	"latitude": 32,
    "longitude": 35.25
};
Country["PT"] = {
	"nameEN": "Portugal",
	"nameAR": "",
	"latitude": 39.5,
    "longitude": -8
};
Country["PW"] = {
	"nameEN": "Palau",
	"nameAR": "",
	"latitude": 7.5,
    "longitude": 134.5
};
Country["PY"] = {
	"nameEN": "Paraguay",
	"nameAR": "",
	"latitude": -23,
    "longitude": -58
};
Country["QA"] = {
	"nameEN": "Qatar",
	"nameAR": "Ù‚Ø·Ø±",
	"latitude": 25.5,
    "longitude": 51.25
};
Country["RE"] = {
	"nameEN": "Reunion",
	"nameAR": "",
	"latitude": -21.1,
    "longitude": 55.6
};
Country["RO"] = {
	"nameEN": "Romania",
	"nameAR": "",
	"latitude": 46,
    "longitude": 25
};
Country["RS"] = {
	"nameEN": "Serbia",
	"nameAR": "",
	"latitude": 44,
    "longitude": 21
};
Country["RU"] = {
	"nameEN": "Russian Federation",
	"nameAR": "Ø±ÙˆØ³ÙŠØ§",
	"latitude": 60,
    "longitude": 100
};
Country["RW"] = {
	"nameEN": "Rwanda",
	"nameAR": "",
	"latitude": -2,
    "longitude": 30
};
Country["SA"] = {
	"nameEN": "Saudi Arabia",
	"nameAR": "Ø§Ù„Ø³Ø¹ÙˆØ¯ÙŠØ©",
	"latitude": 25,
    "longitude": 45
};
Country["SB"] = {
	"nameEN": "Solomon Islands",
	"nameAR": "",
	"latitude": -8,
    "longitude": 159
};
Country["SC"] = {
	"nameEN": "Seychelles",
	"nameAR": "",
	"latitude": -4.5833,
    "longitude": 55.6667
};
Country["SD"] = {
	"nameEN": "Sudan",
	"nameAR": "Ø§Ù„Ø³ÙˆØ¯Ø§Ù†",
	"latitude": 15,
    "longitude": 30
};
Country["SE"] = {
	"nameEN": "Sweden",
	"nameAR": "Ø§Ù„Ø³ÙˆÙŠØ¯",
	"latitude": 62,
    "longitude": 15
};
Country["SG"] = {
	"nameEN": "Singapore",
	"nameAR": "",
	"latitude": 1.3667,
    "longitude": 103.8
};
Country["SH"] = {
	"nameEN": "Saint Helena",
	"nameAR": "",
	"latitude": -15.9333,
    "longitude": -5.7
};
Country["SI"] = {
	"nameEN": "Slovenia",
	"nameAR": "",
	"latitude": 46,
    "longitude": 15
};
Country["SJ"] = {
	"nameEN": "Svalbard and Jan Mayen",
	"nameAR": "",
	"latitude": 78,
    "longitude": 20
};
Country["SK"] = {
	"nameEN": "Slovakia",
	"nameAR": "",
	"latitude": 48.6667,
    "longitude": 19.5
};
Country["SL"] = {
	"nameEN": "Sierra Leone",
	"nameAR": "",
	"latitude": 8.5,
    "longitude": -11.5
};
Country["SM"] = {
	"nameEN": "San Marino ",
	"nameAR": "",
	"latitude": 43.7667,
    "longitude": 12.4167
};
Country["SN"] = {
	"nameEN": "Senegal",
	"nameAR": "Ø§Ù„Ø³Ù†ÙŠØºØ§Ù„",
	"latitude": 14,
    "longitude": -14
};
Country["SO"] = {
	"nameEN": "Somalia",
	"nameAR": "Ø§Ù„ØµÙˆÙ…Ø§Ù„",
	"latitude": 10,
    "longitude": 49
};
Country["SR"] = {
	"nameEN": "Suriname",
	"nameAR": "",
	"latitude": 4,
    "longitude": -56
};
Country["ST"] = {
	"nameEN": "Sao Tome and Principe",
	"nameAR": "",
	"latitude": 1,
    "longitude": 7
};
Country["SV"] = {
	"nameEN": "El Salvador",
	"nameAR": "",
	"latitude": 13.8333,
    "longitude": -88.9167
};
Country["SY"] = {
	"nameEN": "Syrian Arab Republic",
	"nameAR": "",
	"latitude": 35,
    "longitude": 38
};
Country["SZ"] = {
	"nameEN": "Swaziland",
	"nameAR": "",
	"latitude": -26.5,
    "longitude": 31.5
};
Country["TC"] = {
	"nameEN": "Turks and Caicos Islands",
	"nameAR": "",
	"latitude": 21.75,
    "longitude": -71.5833
};
Country["TD"] = {
	"nameEN": "Chad",
	"nameAR": "",
	"latitude": 15,
    "longitude": 19
};
Country["TF"] = {
	"nameEN": "French Southern Territories",
	"nameAR": "",
	"latitude": -43,
    "longitude": 67
};
Country["TG"] = {
	"nameEN": "Togo",
	"nameAR": "",
	"latitude": 8,
    "longitude": 1.1667
};
Country["TH"] = {
	"nameEN": "Thailand",
	"nameAR": "",
	"latitude": 15,
    "longitude": 100
};
Country["TJ"] = {
	"nameEN": "Tajikistan",
	"nameAR": "",
	"latitude": 39,
    "longitude": 71
};
Country["TK"] = {
	"nameEN": "Tokelau",
	"nameAR": "",
	"latitude": -9,
    "longitude": -172
};
Country["TM"] = {
	"nameEN": "Turkmenistan",
	"nameAR": "",
	"latitude": 40,
    "longitude": 60
};
Country["TN"] = {
	"nameEN": "Tunisia",
	"nameAR": "ØªÙˆÙ†Ø³",
	"latitude": 34,
    "longitude": 9
};
Country["TO"] = {
	"nameEN": "Tonga",
	"nameAR": "",
	"latitude": -20,
    "longitude": -175
};
Country["TR"] = {
	"nameEN": "Turkey",
	"nameAR": "ØªØ±ÙƒÙŠØ§",
	"latitude": 39,
    "longitude": 35
};
Country["TT"] = {
	"nameEN": "Trinidad and Tobago",
	"nameAR": "",
	"latitude": 11,
    "longitude": -61
};
Country["TV"] = {
	"nameEN": "Tuvalu",
	"nameAR": "",
	"latitude": -8,
    "longitude": 178
};
Country["TW"] = {
	"nameEN": "Taiwan (Province of China)",
	"nameAR": "",
	"latitude": 23.5,
    "longitude": 121
};
Country["TZ"] = {
	"nameEN": "Tanzania, United Republic of",
	"nameAR": "",
	"latitude": -6,
    "longitude": 35
};
Country["UA"] = {
	"nameEN": "Ukraine",
	"nameAR": "",
	"latitude": 49,
    "longitude": 32
};
Country["UG"] = {
	"nameEN": "Uganda",
	"nameAR": "",
	"latitude": 1,
    "longitude": 32
};
Country["UM"] = {
	"nameEN": "United States Minor Outlying Islands",
	"nameAR": "",
	"latitude": 19.2833,
    "longitude": 166.6
};
Country["US"] = {
	"nameEN": "United States",
	"nameAR": "Ø§Ù„ÙˆÙ„Ø§ÙŠØ§Øª Ø§Ù„Ù…ØªØ­Ø¯Ø© Ø§Ù„Ø£Ù…Ø±ÙŠÙƒÙŠØ©",
	"latitude": 38,
    "longitude": -97
};
Country["UY"] = {
	"nameEN": "Uruguay",
	"nameAR": "",
	"latitude": -33,
    "longitude": -56
};
Country["UZ"] = {
	"nameEN": "Uzbekistan",
	"nameAR": "",
	"latitude": 41,
    "longitude": 64
};
Country["VA"] = {
	"nameEN": "Holy See (Vatican City State)",
	"nameAR": "",
	"latitude": 41.9,
    "longitude": 12.45
};
Country["VC"] = {
	"nameEN": "Saint Vincent and the Grenadines",
	"nameAR": "",
	"latitude": 13.25,
    "longitude": -61.2
};
Country["VE"] = {
	"nameEN": "Venezuela",
	"nameAR": "ÙÙ†Ø²ÙˆÙŠÙ„Ø§",
	"latitude": 8,
    "longitude": -66
};
Country["VG"] = {
	"nameEN": "Virgin Islands, British",
	"nameAR": "",
	"latitude": 18.5,
    "longitude": -64.5
};
Country["VI"] = {
	"nameEN": "Virgin Islands, U.S.",
	"nameAR": "",
	"latitude": 18.3333,
    "longitude": -64.8333
};
Country["VN"] = {
	"nameEN": "Vietnam",
	"nameAR": "",
	"latitude": 16,
    "longitude": 106
};
Country["VU"] = {
	"nameEN": "Vanuatu",
	"nameAR": "",
	"latitude": -16,
    "longitude": 167
};
Country["WF"] = {
	"nameEN": "Wallis and Futuna",
	"nameAR": "",
	"latitude": -13.3,
    "longitude": -176.2
};
Country["WS"] = {
	"nameEN": "Samoa",
	"nameAR": "",
	"latitude": -13.5833,
    "longitude": -172.3333
};
Country["YE"] = {
	"nameEN": "Yemen",
	"nameAR": "Ø§Ù„ÙŠÙ…Ù†",
	"latitude": 15,
    "longitude": 48
};
Country["YT"] = {
	"nameEN": "Mayotte",
	"nameAR": "",
	"latitude": -12.8333,
    "longitude": 45.1667
};
Country["ZA"] = {
	"nameEN": "South Africa",
	"nameAR": "",
	"latitude": -29,
    "longitude": 24
};
Country["ZM"] = {
	"nameEN": "Zambia",
	"nameAR": "",
	"latitude": -15,
    "longitude": 30
};
Country["ZW"] = {
	"nameEN": "Zimbabwe",
	"nameAR": "Ø²ÙŠÙ…Ø¨Ø§Ø¨ÙˆÙŠ",
	"latitude": -20,
    "longitude": 30
};

function showMap(divID,mapData,title){
var map;
var min = Infinity;
var max = -Infinity;


// get min and max values
for (var i = 0; i < mapData.length; i++) {
    var value = parseInt(mapData[i].value);
    if (value < min) {
        min = value;
    }
    if (value > max) {
        max = value;
    }
}

// build map
AmCharts.ready(function() {
    AmCharts.theme = AmCharts.themes.dark;
    map = new AmCharts.AmMap();
    map.pathToImages = ""; //https://www.amcharts.com/lib/3/images/

    map.addTitle(title, 14);
    if (mapData.length == 0) map.addTitle("No Activity Logs", 11,"red");
    map.areasSettings = {
        unlistedAreasColor: "#000000",
        unlistedAreasAlpha: 0.15
    };
    map.hideBalloonTime = 50;
    map.imagesSettings.balloonText = "<span style='font-size:14px;'><b>[[title]]</b>: [[value]]</span>";
	map.imagesSettings.alpha = 0.6;
	//map.imagesSettings.hideBalloonTime = 3000;

    var dataProvider = {
        mapVar: AmCharts.maps.worldLow,
        images: []
    }

    var minBulletSize = 6;
    var maxBulletSize = max*6/min>50?50:max*6/min;   //default var maxBulletSize = 70;    
    // it's better to use circle square to show difference between values, not a radius
    var maxSquare = maxBulletSize * maxBulletSize * 2 * Math.PI;
    var minSquare = minBulletSize * minBulletSize * 2 * Math.PI;
	
    var Colors = ["#2614D3", "#007da9", "#0000FF", "#008080", "#008000", "#800000", "#FF0000", "#808080", "#2471a3", "#17a589", "#229954", "#d4ac0d", "#32f31d", "#1d9df3", "#9d1df3", "#f31dc8", "#f3321d", "#f3731d", "#2929ff", "#19f4f4", "#ea1472", "#b00ddf", "#df0dcf", "#df0d90", "#df0d51", "#df0d12", "#df470d", "#df860d", "#3cdf0d", "#0ddf1d", "#0ddf5c", "#0ddf9b", "#0ebdb9", "#0d66df", "#0d27df", "#320ddf", "#710ddf", "#8b27f9", "#ca27f9", "#f927e9", "#f927aa", "#f9276b", "#f9272c", "#f96127", "#f9a027", "#56f927", "#27f937", "#27f976", "#27f9b5", "#25c6c2", "#27bff9", "#2780f9", "#2741f9", "#4c27f9", "#5b36ff", "#9a36ff", "#d936ff", "#ff36f8", "#ff36b9", "#ff367a", "#ff363b", "#ff7036", "#ffaf36", "#65ff36", "#36ff46", "#2fcb6d", "#cd2e2e", "#36ffff", "#36ceff", "#368fff", "#3650ff", "#405aff", "#6240ff", "#9f40ff", "#db40ff", "#ff40fb", "#ff40bf", "#ff4082", "#ff4046", "#ff7640", "#ffb340", "#ffef40", "#e7ff40", "#aaff40", "#6eff40", "#40ff4e", "#40ff8b", "#40ffc7", "#40ffff", "#40d3ff", "#4096ff", "#56acff", "#5673ff", "#7356ff", "#ac56ff", "#e556ff", "#ff56ff", "#ff56d2", "#ff5699", "#ff5660", "#ff8656", "#ffbf56", "#fff856"];
    
    /*var chart = am4core.create(divID, am4maps.MapChart);  //requis files core.js & maps.js 
    var rr = "";
    for (var i = 0; i < 2000; i++) {
        rr += "<div style=\"background-color:" + chart.colors.getIndex(i) + "\">" + chart.colors.getIndex(i) + "</div>";
    }
    document.write(rr);
    return;*/
    // create circle for each country
	/*
    for (var i = 0; i < mapData.length; i++) {
        var dataItem = mapData[i];
        var value = dataItem.value;
        // calculate size of a bubble
        var square = (value - min) / (max - min) * (maxSquare - minSquare) + minSquare;
        if (square < minSquare) {
            square = minSquare;
        }
        var size = Math.sqrt(square / (Math.PI * 2));
        var id = dataItem.code;

        dataProvider.images.push({
            type: "circle",
            width: size,
            height: size,
            color: dataItem.color !== undefined && dataItem.color !== null && dataItem.color !== ""?dataItem.color:chart.colors.getIndex(i),
            longitude: Country[id].longitude,
            latitude: Country[id].latitude,
            title: typeof Country[id].nameEN === 'undefined' || variable === null ? id : Country[id].nameEN,
            value: value
        });
    }*/


    // the following code uses circle radius to show the difference
    
    for (var i = 0; i < mapData.length; i++) {
        var dataItem = mapData[i];
		if(dataItem.code.length==2){
			try {
        var value = parseInt(dataItem.value);
        // calculate size of a bubble
        /*var size = (value - min) / (max - min) * (maxBulletSize - minBulletSize) + minBulletSize;
        if (size < minBulletSize) {
            size = minBulletSize;
        }*/ //add by me
		var square = (value - min) / (max - min) * (maxSquare - minSquare) + minSquare;
        if (square < minSquare) {
            square = minSquare;
        }
        var size = Math.sqrt(square / (Math.PI * 2));
        var id = dataItem.code;
        dataProvider.images.push({
            type: "circle",
            width: size,
            height: size,
            color: dataItem.color !== undefined && dataItem.color !== null && dataItem.color !== "" ? dataItem.color : Colors[i%Colors.length],
            longitude: Country[id].longitude,
            latitude: Country[id].latitude,
            title: typeof Country[id].nameEN === 'undefined' || Country[id].nameEN === null ? id : Country[id].nameEN,
            value: value
        });
			}
			catch(err) {}
		}
    }/**/

    map.dataProvider = dataProvider;

    map.write(divID);
	});
}