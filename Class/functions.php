<?php

$districtArray = array(
    "District",
    "Achham",
    "Arghakhanchi",
    "Baglung",
    "Baitadi",
    "Bajhang",
    "Bajura",
    "Banke",
    "Bara",
    "Bardiya",
    "Bhaktapur",
    "Bhojpur",
    "Chitwan",
    "Dadeldhura",
    "Dailekh",
    "Dang",
    "Darchula",
    "Dhading",
    "Dhankuta",
    "Dhanusha",
    "Dolakha",
    "Dolpa",
    "Doti",
    "Gorkha",
    "Gulmi",
    "Humla",
    "Ilam",
    "Jajarkot",
    "Jhapa",
    "Jumla",
    "Kailali",
    "Kalikot",
    "Kanchanpur",
    "Kapilvastu",
    "Kaski",
    "Kathmandu",
    "Kavrepalanchok",
    "Khotang",
    "Lalitpur",
    "Lamjung",
    "Mahottari",
    "Makwanpur",
    "Manang",
    "Morang",
    "Mugu",
    "Mustang",
    "Myagdi",
    "Nawalparasi",
    "Nuwakot",
    "Okhaldhunga",
    "Palpa",
    "Panchthar",
    "Parbat",
    "Parsa",
    "Pyuthan",
    "Ramechhap",
    "Rasuwa",
    "Rautahat",
    "Rolpa",
    "Rukum",
    "Rupandehi",
    "Salyan",
    "Sankhuwasabha",
    "Saptari",
    "Sarlahi",
    "Sindhuli",
    "Sindhupalchok",
    "Siraha",
    "Solukhumbu",
    "Sunsari",
    "Surkhet",
    "Syangja",
    "Tanahu",
    "Taplejung",
    "Tehrathum",
    "Udayapur",
    "West Rukum",
    "Dhawalagiri (formerly Mustang)"
);

$amenityArrayList = array(
    "Air Conditioning",
    "Balcony",
    "Fireplace",
    "Gardening",
    "Internet",
    "Laundry",
    "Parking",
    "Pets Allowed",
    "Prompt Repair Service",
    "Security",
    "Solar Heating",
    "Swimming Pool",
);

function destroy_session_function()
{
    session_destroy();
}

function returnFormattedName($first, $middle, $last)
{
    $first = trim(strtolower($first));
    if ($middle != '')
        $middle = trim(strtolower($middle));
    $last = trim(strtolower($last));

    $first[0] = strtoupper($first[0]);
    if ($middle != '')
        $middle[0] = strtoupper($middle[0]);
    else
        $middel = 'null';
    $last[0] = strtoupper($last[0]);

    $fullName = "";

    if ($first == "null" && $middle == "null" && $last == "null") { //all not set
        $fullName = "null";
    } else if ($first != "null" && $middle == "null" && $last == "null") { //only first name set
        $fullName = $first;
    } else if ($first == "null" && $middle != "null" && $last == "null") { //only middle name set
        $fullName = $middle;
    } else if ($first == "null" && $middle == "null" && $last != "null") { //only last name set
        $fullName = $last;
    } else if ($first != "null" && $middle != "null" && $last == "null") { //fist & middle name set
        $fullName = $first . ' ' . $middle;
    } else if ($first != "null" && $middle == "null" && $last != "null") { //first $ last name set
        $fullName = $first . ' ' . $last;
    } else if ($first == "null" && $middle != "null" && $last != "null") { //second $ last name set
        $fullName = $middle . ' ' . $last;
    } else { //all name set
        $fullName = $first . ' ' . $middle . ' ' . $last;
    }
    return $fullName;
}

function returnFormattedAddress($province, $district, $atomAddress, $wardNumber)
{
    $address = "NULL";
    // $address = returnFormattedString(returnArrayValue("province", $province)) . ', ' . returnFormattedString(returnArrayValue("district", $district)) . ', ' . returnFormattedString($atomAddress) . ', ' . ($wardNumber);
    $address = ucfirst($atomAddress) . '-' . ($wardNumber) . ', ' . returnFormattedString(returnArrayValue("district", $district)) . ', ' . returnFormattedString(returnArrayValue("province", $province));
    return $address;
}
function returnFormattedString($string)
{
    $finalString = trim(strtolower($string));
    $finalString[0] = strtoupper($finalString[0]);

    if ($finalString == 'null') {
        $finalString = "";
    }

    return $finalString;
}


function returnArrayValue($whichSelect, $index)
{
    global $districtArray;
    global $amenityArrayList;

    $value = "Something";
    $provinceArray = array("Province", "Koshi", "Madhesh", "Bagmati", "Gandaki", "Lumbini", "Karnali", "Sudurpaschim");
    if ($whichSelect == "province")
        $value = $provinceArray[$index];
    elseif ($whichSelect == "district")
        $value = $districtArray[$index];
    elseif ($whichSelect == "amenity")
        $value = $amenityArrayList[$index];
    else
        $value = "NULL";

    return $value;
}

function returnArrayIndex($whichSelect, $value)
{
    global $districtArray;
    global $amenityArrayList;

    $provinceArray = array("Province", "Koshi", "Madhesh", "Bagmati", "Gandaki", "Lumbini", "Karnali", "Sudurpaschim");

    if ($whichSelect == "province") {
        $index = array_search($value, $provinceArray);
    } elseif ($whichSelect == "district") {
        $index = array_search($value, $districtArray);
    }

    return $index;
}

function returnIconName($amenityName)
{
    if ($amenityName == "Air Conditioning")
        $amenityIconName = "air-conditioner.png";
    elseif ($amenityName == "Balcony")
        $amenityIconName = "balcony.png";
    elseif ($amenityName == "Fireplace")
        $amenityIconName = "fire-place.png";
    elseif ($amenityName == "Gardening")
        $amenityIconName = "gardening.png";
    elseif ($amenityName == "Internet")
        $amenityIconName = "internet.png";
    elseif ($amenityName == "Laundry")
        $amenityIconName = "laundry.png";
    elseif ($amenityName == "Parking")
        $amenityIconName = "parking.png";
    elseif ($amenityName == "Pets Allowed")
        $amenityIconName = "pets-allowed.png";
    elseif ($amenityName == "Prompt Repair Service")
        $amenityIconName = "prompt-repair-service.png";
    elseif ($amenityName == "Security")
        $amenityIconName = "security.png";
    elseif ($amenityName == "Solar Heating")
        $amenityIconName = "solar-heating.png";
    elseif ($amenityName == "Swimming Pool")
        $amenityIconName = "swimming-pool.png";
    else
        $amenityIconName = "NULL";

    return $amenityIconName;
}

function returnFormattedPrice($price)
{
    $x = strval($price);

    $digitCount = strlen($x);
    $formattedPrice = "";

    switch ($digitCount) {
        case 4:
            $formattedPrice = 'NRs. ' . $x[0] . "," . $x[1] . $x[2] . $x[3];
            break;
        case 5:
            $formattedPrice = 'NRs. ' . $x[0] . $x[1] . "," . $x[2] . $x[3] . $x[4];
            break;
        case 6:
            $formattedPrice = 'NRs. ' . $x[0] . "," . $x[1] . $x[2] . "," . $x[3] . $x[4] . $x[5];
            break;
        case 7:
            $formattedPrice = 'NRs. ' . $x[0] . $x[1] . "," . $x[2] . $x[3] . "," . $x[4] . $x[5] . $x[6];
            break;
        case 8:
            $formattedPrice = 'NRs. ' . $x[0] . "," . $x[1] . $x[2] . "," . $x[3] . $x[4] . "," . $x[5] . $x[6] . $x[7];
            break;
        case 9:
            $formattedPrice = 'NRs. ' . $x[0] . $x[1] . "," . $x[2] . $x[3] . "," . $x[4] . $x[5] . "," . $x[6] . $x[7] . $x[8];
            break;
        case 10:
            $formattedPrice = 'NRs. ' . $x[0] . "," . $x[1] . $x[2] . "," . $x[3] . $x[4] . "," . $x[5] . $x[6] . "," . $x[7] . $x[8] . $x[9];
            break;
        default:
            $formattedPrice = 'NRS. ' . $x;
    }

    return $formattedPrice;
}
?>

<!-- Amenities List
Air Conditioning
Balcony
Fireplace
Garden
Internet
Laundry
Parking
Pets Allowed
Prompt Repair Service
Security
Solar Heating
Swimming Pool -->

<?php
function getRoomIdArray($userId)
{
    include_once 'house_class.php';
    include_once 'user_class.php';

    $house = new House();
    $room = new Room();

    $myRoomIdArray = [];
    $myHouseIdArray = $house->fetchLandlordHouseIdArray($userId);
    if (sizeof($myHouseIdArray) > 0) {
        foreach ($myHouseIdArray as $myHouseId) {
            $tempRoomIdArray = $room->fetchLandlordRoomIdArray($myHouseId);
            if (sizeof($tempRoomIdArray) > 0) {
                foreach ($tempRoomIdArray as $tempRoomId)
                    $myRoomIdArray[] = $tempRoomId;
            }
        }
    }
    return $myRoomIdArray;
}
?>