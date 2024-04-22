<?php
include_once 'connection_class.php';

class House extends DatabaseConnection
{
    public $houseId;
    public $ownerId;
    public $houseIdentity;
    public $district;
    public $areaName;
    public $locationCoordinate;

    public $allAmenities;
    public $generalRequirement;
    public $houseState;
    public $housePhoto1;
    public $housePhoto2;
    public $housePhoto3;
    public $housePhoto4;
    public $housePhotoArray = [];

    public $registerDate;


    public function setHouse($ownerId, $houseIdentity, $district, $areaName, $locationCoordinate, $allAmenities, $generalRequirement, $houseState, $registerDate)
    {
        $this->ownerId = $ownerId;
        $this->houseIdentity = $this->conn->real_escape_string($houseIdentity);
        $this->district = $district;
        $this->areaName = $this->conn->real_escape_string($areaName);
        $this->locationCoordinate = $locationCoordinate;
        $this->allAmenities = $this->conn->real_escape_string($allAmenities);
        $this->generalRequirement = $this->conn->real_escape_string($generalRequirement);
        $this->houseState = $houseState;
        $this->registerDate = $registerDate;
    }

    public function registerHouse()
    {
        $query = "insert into `house` (owner_id, house_identity, district, area_name, location_coordinate, all_amenities, general_requirement, house_state, register_date) 
                values('$this->ownerId','$this->houseIdentity','$this->district', '$this->areaName', '$this->locationCoordinate', '$this->allAmenities','$this->generalRequirement', '$this->houseState', '$this->registerDate')";

        $response = mysqli_query($this->conn, $query);

        return $response ? $this->getImmediateHouseId() : false;
    }

    public function addHousePhoto($houseId)
    {
        $query = "insert into `house_photo` (house_id, house_photo) values ('$houseId','$this->housePhoto1')";
        $response = mysqli_query($this->conn, $query);

        $query = "insert into `house_photo` (house_id, house_photo) values ('$houseId','$this->housePhoto2')";
        $response = mysqli_query($this->conn, $query);

        $query = "insert into `house_photo` (house_id, house_photo) values ('$houseId','$this->housePhoto3')";
        $response = mysqli_query($this->conn, $query);

        $query = "insert into `house_photo` (house_id, house_photo) values ('$houseId','$this->housePhoto4')";
        $response = mysqli_query($this->conn, $query);
    }

    private function getImmediateHouseId()
    {
        $query = "select house_id from `house` where owner_id = '$this->ownerId' and register_date = '$this->registerDate'";
        $result = $this->conn->query($query);
        $houseIdArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $houseIdArray[] = $row;

        foreach ($houseIdArray as $temp)
            $houseId = $temp['house_id'];

        return $houseId;
    }

    public function countHouse($which)
    {
        if ($which == "all")
            $query = 'select house_id from `house`';
        else
            $query = "select house_id from `house` where house_state = '$which'";

        $response = mysqli_query($this->conn, $query);
        return mysqli_num_rows($response);
    }

    public function countUserHouse($userId, $which)
    {
        if ($which == "all")
            $query = "select * from `house` where owner_id = '$userId'";
        else
            $query = "select * from `house` where owner_id = '$userId' and house_state= '$which'";

        $response = mysqli_query($this->conn, $query);
        return mysqli_num_rows($response);
    }

    public function getLocation($houseId)
    {
        $areaName = "Not-Found";
        $query = "select area_name, district from `house` where house_id = '$houseId'";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            while ($row = mysqli_fetch_array($result))
                $areaName = ucfirst($row['area_name']) . ', ' . returnArrayValue('district', $row['district']);

        return $areaName;
    }

    public function getDistrict($houseId)
    {
        $distictNumber = 0;
        $query = "select district from `house` where house_id = '$houseId'";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            while ($row = mysqli_fetch_array($result))
                $distictNumber = $row['district'];

        return $distictNumber;
    }

    public function getAreaName($houseId)
    {
        $areaName = "Unknown";
        $query = "select area_name from `house` where house_id = '$houseId'";
        $result = mysqli_query($this->conn, $query);

        if ($result)
            while ($row = mysqli_fetch_array($result))
                $areaName = $row['area_name'];

        return $areaName;
    }

    public function getOwnerId($houseId)
    {
        $ownerId = 0;
        $query = "select owner_id from `house` where house_id = '$houseId'";
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            while ($row = mysqli_fetch_array($result))
                $ownerId = $row['owner_id'];
        }
        return $ownerId;
    }

    public function getGeneralRequirement($houseId)
    {
        $generalRequirement = "Not-Found";
        $query = "select general_requirement from `house` where house_id = '$houseId'";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            while ($row = mysqli_fetch_array($result))
                $generalRequirement = $row['general_requirement'];

        return $generalRequirement;
    }

    public function getHouseIdentity($houseId)
    {
        $identityName = "Not-Found";
        $query = "select house_identity from `house` where house_id = '$houseId'";

        $result = mysqli_query($this->conn, $query);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                $row = mysqli_fetch_array($result);
                $identityName = $row['house_identity'];
            }
        }

        return htmlspecialchars_decode($identityName, ENT_QUOTES);
    }


    public function fetchAllHouses($whoseHouse)
    {
        if ($whoseHouse == 'all')
            $query = "select * from `house`";
        else
            $query = "select * from `house` where owner_id = '$whoseHouse'";

        $result = $this->conn->query($query);

        $house = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $house[] = $row;

        return $house;
    }

    public function fetchHouse($houseId)
    {
        $query = "select * from `house` where house_id = $houseId";
        $response = mysqli_query($this->conn, $query);

        if ($response) {
            if ($response->num_rows > 0) {
                $row = mysqli_fetch_assoc($response);

                $this->setHouse($row['owner_id'], $row['house_identity'], $row['district'], $row['area_name'], $row['location_coordinate'], $row['all_amenities'], $row['general_requirement'], $row['house_state'], $row['register_date']);

                // fetching house photos
                $query = "select house_photo from `house_photo` where house_id = $houseId";
                $response = mysqli_query($this->conn, $query);

                if (mysqli_num_rows($response) > 0) {
                    while ($row = $response->fetch_assoc())
                        $this->housePhotoArray[] = $row;
                } else {
                    for ($i = 0; $i < 4; $i++) {
                        $this->housePhotoArray[]['house_photo'] = "blank.jpg";
                    }
                }
            }
        }
    }

    public function houseState($houseId)
    {
        $houseId = $this->conn->real_escape_string($houseId);

        $query = "select house_state from `house` where house_id = '$houseId'";
        $response = mysqli_query($this->conn, $query);
        $count = mysqli_num_rows($response);
        if ($count > 0) {
            $row = mysqli_fetch_assoc($response);
            if ($row['house_state'] == 0)
                return "Unapproved";
            elseif ($row['house_state'] == 1)
                return "Approved";
            else
                return "Suspended";
        }
    }

    public function updateHouse($houseId)
    {
        $houseId = $this->conn->real_escape_string($houseId);
        $query = "update `house` set house_identity = '$this->houseIdentity', district = '$this->district', area_name = '$this->areaName', location_coordinate = '$this->locationCoordinate', all_amenities = '$this->allAmenities', general_requirement = '$this->generalRequirement' where house_id = '$houseId'";
        $result = mysqli_query($this->conn, $query);
        return ($result) ? true : false;
    }

    public function searchHouse($content)
    {
        $content = $this->conn->real_escape_string($content);

        $districtInteger = returnArrayIndex("district", returnFormattedString($content));
        $query = "select * from `house` where house_id = '$content' or house_identity = '$content' or district like '$districtInteger' or area_name like '$content'";
        $result = $this->conn->query($query);
        $houses = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $houses[] = $row;

        return $houses;
    }

    public function returnHouseareaName($houseId)
    {
        $houseId = $this->conn->real_escape_string($houseId);

        $query = "select location_name from `house` where house_id = '$houseId'";
        $result = mysqli_query($this->conn, $query);
        while ($row = $result->fetch_assoc()) {
            return $row['location_name'];
        }
    }

    // valid house check: url tampering test
    public function isValidHouse($houseId, $landlordId)
    {
        if ($landlordId == 0)
            $query = "select * from `house` where house_id = '$houseId'";
        else
            $query = "select * from `house` where house_id = '$houseId' and owner_id = '$landlordId'";

        $response = mysqli_query($this->conn, $query);
        $count = mysqli_num_rows($response);

        return ($count != 0) ? true : false;
    }

    public function fetchLandlordHouseIdArray($ownerId)
    {
        $query = "select house_id from `house` where owner_id = '$ownerId'";
        $result = $this->conn->query($query);

        $houseIdArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $houseIdArray[] = $row['house_id'];

        return $houseIdArray;
    }

    // returning house id
    public function returnMyHouseIdArray($landlordId)
    {
        $myHouseIdArray = [];
        $query = "select * from `house` where owner_id = '$landlordId'";
        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $myHouseIdArray[] = $row['house_id'];
            }
        }

        return $myHouseIdArray;
    }

    public function houseOperation($task, $houseId){
        $query = "";
        if($task == 'suspend'){
            $query = "update `house` set house_state = 0 where house_id = '$houseId'";
        }elseif($task == 'verify'){
            $query = "update `house` set house_state = 1 where house_id = '$houseId'";
        }elseif($task == 'remove'){
            $query = "delete from `house` where house_id = '$houseId'";
        }
        $result = $this->conn->query($query);
        return $result?true:false;
    }

    public function updateHousePhoto($oldHousePhoto, $newHousePhoto){
        $query = "update `house_photo` set house_photo = '$newHousePhoto' where house_photo = '$oldHousePhoto'";
        $result = $this->conn->query($query);
        return $result ? true: false;
    }
}

class Room extends House
{
    public $roomId;
    public $houseId;
    public $roomNumber;
    public $numberOfRoom;
    public $rentAmount;
    public $roomType;
    public $bhk;
    public $furnishing;

    public $floor;
    public $amenities;

    public $requirement;
    public $isAcquired;
    public $roomState;
    public $roomPhoto1;
    public $roomPhoto2;
    public $roomPhoto3;
    public $roomPhoto4;

    public $roomPhotoArray = [];
    public $tenantId; // userId
    public $registerDate;

    public function setRoom($houseId, $roomNumber, $rentAmount, $roomType, $furnishing, $bhk, $numberOfRoom, $floor, $amenities, $requirement, $isAcquired, $tenantId, $roomState, $registerDate)
    {
        $this->houseId = $houseId;
        $this->roomNumber = $roomNumber;
        $this->rentAmount = $rentAmount;
        $this->roomType = $roomType;
        $this->furnishing = $furnishing;
        $this->bhk = $bhk;
        $this->floor = $floor;
        $this->numberOfRoom = $numberOfRoom;
        $this->amenities = $this->conn->real_escape_string($amenities);
        $this->requirement = $this->conn->real_escape_string($requirement);
        $this->isAcquired = $isAcquired;
        $this->roomState = $roomState;
        $this->tenantId = $tenantId;
        $this->registerDate = $registerDate;
    }


    public function registerRoom()
    {
        $query = "insert into `room` (house_id, room_number, rent_amount, room_type, furnishing, bhk, number_of_room, floor, amenities, requirement, is_acquired, tenant_id, room_state, register_date) 
                                values('$this->houseId', '$this->roomNumber', '$this->rentAmount', '$this->roomType', '$this->furnishing', '$this->bhk', '$this->numberOfRoom', '$this->floor', '$this->amenities', '$this->requirement', '$this->isAcquired', '$this->tenantId', '$this->roomState', '$this->registerDate')";

        $response = mysqli_query($this->conn, $query);

        return $response ? $this->getImmediateRoomId() : false;
    }

    public function addRoomPhoto($roomId)
    {
        $query = "insert into `room_photo` (room_id, room_photo) values ('$roomId','$this->roomPhoto1')";
        $response = mysqli_query($this->conn, $query);

        $query = "insert into `room_photo` (room_id, room_photo) values ('$roomId','$this->roomPhoto2')";
        $response = mysqli_query($this->conn, $query);

        $query = "insert into `room_photo` (room_id, room_photo) values ('$roomId','$this->roomPhoto3')";
        $response = mysqli_query($this->conn, $query);

        $query = "insert into `room_photo` (room_id, room_photo) values ('$roomId','$this->roomPhoto4')";
        $response = mysqli_query($this->conn, $query);
    }

    private function getImmediateRoomId()
    {
        $query = "select room_id  from `room` where house_id = '$this->houseId' and requirement = '$this->requirement' and  register_date = '$this->registerDate'";
        $result = $this->conn->query($query);
        $roomIdArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $roomIdArray[] = $row;

        foreach ($roomIdArray as $temp)
            $roomId = $temp['room_id'];

        return $roomId;
    }

    public function countRoom($landlordId, $roomId, $type)
    {
        if ($roomId == "allHouses")
            $query = "select room_id, house_id from `room` where room_id = room_id";
        else
            $query = "select room_id, house_id from `room` where room_id = '$roomId'";

        if ($type != "allTypes") {
            if ($type == "unverified")
                $query = $query . " and room_state = '0'";
            else if ($type == "verified")
                $query = $query . " and room_state = '1'";
            else if ($type == "suspended")
                $query = $query . " and room_state = '2'";
            else if ($type == "unacquired")
                $query = $query . " and is_acquired = '0'";
            else if ($type == "acquired")
                $query = $query . " and is_acquired = '1'";
        }

        $result = mysqli_query($this->conn, $query);

        $houseIdArray = [];
        if ($landlordId != "admin") {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc())
                    // check for the specific landlord
                    if ($this->getOwnerId($row['house_id']) == $landlordId)
                        $houseIdArray[] = $row['room_id'];
            }
        } else {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc())
                    $houseIdArray[] = $row['room_id'];
            }
        }

        return sizeof($houseIdArray);
    }

    // for counting the number of houses
    public function countRoomOfThisHouse($houseId)
    {
        $houseId = $this->conn->real_escape_string($houseId);

        $query = "select room_id from `room` where house_id = '$houseId'";
        $response = mysqli_query($this->conn, $query);
        return mysqli_num_rows($response);
    }

    public function getRoomPhoto($roomId)
    {
        $query = "select room_photo from `room_photo` where room_id = '$roomId'";
        $response = mysqli_query($this->conn, $query);

        $this->roomPhotoArray = [];

        if (mysqli_num_rows($response) > 0)
            while ($row = $response->fetch_assoc())
                $this->roomPhotoArray[] = $row;

        $roomFirstImage = (sizeof($this->roomPhotoArray) > 0) ? $this->roomPhotoArray[0]['room_photo'] : "blank.jpg";
        return $roomFirstImage;
    }

    function fetchAllRoom($whoseHouse)
    {
        $query = "select * from `room`";
        $rooms = [];
        $result = $this->conn->query($query);

        if ($whoseHouse != "admin") {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($this->getOwnerId($row['house_id']) == $whoseHouse)
                        $rooms[] = $row;
                }
            }
            return $rooms;
        } else {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $rooms[] = $row;
                }
            }
            return $rooms;
        }
    }

    function fetchRoomsForTenant()
    {
        $query = "select * from `room` where room_state = 1 and is_acquired = 0";

        $result = $this->conn->query($query);

        $rooms = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $rooms[] = $row;

        return $rooms;
    }

    public function fetchRoom($roomId)
    {
        $query = "select * from `room` where room_id  = '$roomId'";
        $response = mysqli_query($this->conn, $query);

        if ($response) {
            if ($response->num_rows > 0) {

                $row = mysqli_fetch_assoc($response);
                $this->roomId = $row['room_id'];
                $this->setRoom($row['house_id'], $row['room_number'], $row['rent_amount'], $row['room_type'], $row['furnishing'], $row['bhk'], $row['number_of_room'], $row['floor'], $row['amenities'], $row['requirement'], $row['is_acquired'], $row['tenant_id'], $row['room_state'], $row['register_date']);

                // fetching room photos
                $query = "select room_photo from `room_photo` where room_id = $roomId";
                $response = mysqli_query($this->conn, $query);

                if (mysqli_num_rows($response) > 0) {
                    while ($row = $response->fetch_assoc()) {
                        $this->roomPhotoArray[] = $row;
                    }
                } else {
                    for ($i = 0; $i < 4; $i++) {
                        $this->roomPhotoArray[]['room_photo'] = "blank.jpg";
                    }
                }
            }
        }
    }

    public function fetchMyRoom($tenantId)
    {
        $query = "select * from `room` where tenant_id = '$tenantId'";
        $result = $this->conn->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->setRoom($row['house_id'], $row['room_number'], $row['rent_amount'], $row['room_type'], $row['furnishing'], $row['bhk'], $row['number_of_room'], $row['floor'], $row['amenities'], $row['requirement'], $row['is_acquired'], $row['tenant_id'], $row['room_state'], $row['register_date']);
            $this->roomId = $row['room_id'];

            // fetching room photos
            $tempRoomId = $row['room_id'];
            $query = "select room_photo from `room_photo` where room_id = '$tempRoomId'";
            $result = mysqli_query($this->conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc())
                    $this->roomPhotoArray[] = $row;
            } else {
                for ($i = 0; $i < 4; $i++) {
                    $this->roomPhotoArray[]['room_photo'] = "blank.jpg";
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function fetchRoomsOfThisHouse($houseId, $fetchAll, $roomId)
    {
        $houseId = $this->conn->real_escape_string($houseId);
        $roomId = $this->conn->real_escape_string($roomId);

        if ($fetchAll)
            $query = "select * from `room` where house_id = '$houseId'";
        else
            $query = "select * from `room` where house_id = '$houseId' and room_id  != '$roomId'";

        $result = $this->conn->query($query);

        $rooms = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $rooms[] = $row;

        return $rooms;
    }

    public function fetchRoomsOfThisHouseForTenant($houseId, $fetchAll, $roomId)
    {
        $houseId = $this->conn->real_escape_string($houseId);
        $roomId = $this->conn->real_escape_string($roomId);

        if ($fetchAll)
            $query = "select * from `room` where house_id = '$houseId' and room_state = 1";
        else
            $query = "select * from `room` where house_id = '$houseId' and room_id  != '$roomId' and room_state = 1";

        $result = $this->conn->query($query);

        $rooms = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $rooms[] = $row;

        return $rooms;
    }

    public function searchRoomForTenant($location, $minRent, $maxRent, $residingRoomId)
    {
        if ($minRent == 0 && $maxRent == 0)
            $query = "select * from `room` where room_state = 1";
        else {
            if ($minRent != 0 && $maxRent == 0)
                $query = "select * from `room` where rent_amount >= '$minRent' and room_state = 1";
            elseif ($minRent == 0 && $maxRent != 0)
                $query = "select * from `room` where rent_amount <= '$maxRent' and room_state = 1";
            else
                $query = "select * from `room` where rent_amount >= '$minRent' and rent_amount <= '$maxRent' and room_state = 1";
        }

        $result = $this->conn->query($query);

        $rooms = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (strtolower($this->getAreaName($row['house_id'])) == strtolower($location)) {
                    if($row['room_id'] != $residingRoomId){
                        $rooms[] = $row;
                    }
                }
            }
        }
        return $rooms;
    }

    public function searchRoom($content)
    {
        $content = $this->conn->real_escape_string($content);

        $query = "select * from `room` where room_id  = '$content' or house_id = '$content' or room_number = '$content'";
        $result = $this->conn->query($query);
        $rooms = [];
        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $rooms[] = $row;
        return $rooms;
    }

    public function updateRoom($roomId)
    {
        $query = "update `room` set room_number = '$this->roomNumber', rent_amount = '$this->rentAmount', room_type = '$this->roomType', furnishing = '$this->furnishing', bhk = '$this->bhk', number_of_room = '$this->numberOfRoom', floor  = '$this->floor', amenities = '$this->amenities', requirement = '$this->requirement' where room_id  = '$roomId'";
        $result = mysqli_query($this->conn, $query);
        return ($result) ? true : false;
    }

    // valid house check: url tampering test
    public function isValidRoom($roomId, $landlordId)
    {
        if ($landlordId == 0)
            $query = "select * from `room` where room_id  = '$roomId'";
        else
            $query = "select * from `room` where room_id  = '$roomId'";

        $response = mysqli_query($this->conn, $query);
        $count = mysqli_num_rows($response);

        return ($count != 0) ? true : false;
    }

    public function fetchLandlordRoomIdArray($houseId)
    {
        $query = "select room_id  from `room` where house_id = '$houseId'";
        $result = $this->conn->query($query);

        $roomIdArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $roomIdArray[] = $row['room_id'];

        return $roomIdArray;
    }

    public function getTenantState($roomId, $tenantId)
    {
        $query = "select room_id  from `room` where room_id  = '$roomId' and tenant_id = '$tenantId'";
        $result = $this->conn->query($query);
        return ($result->num_rows > 0) ? true : false;
    }

    public function getTenantId($roomId){
        $tenantId = 0;
        $query = "select tenant_id from `room` where room_id = '$roomId' order by room_id desc limit 1";
        $result = $this->conn->query($query);
        if($result){
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $tenantId = $row['tenant_id'];
            }
        }
        return $tenantId;
    }

    public function getResidingRoomId($tenantId){
        $residingRoomId = 0;
        $query = "select room_id from `room` where tenant_id = '$tenantId' order by room_id limit 1";
        $result = $this->conn->query($query);
        if($result){
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $residingRoomId = $row['room_id'];
            }
        }

        return $residingRoomId;
    }

    public function isTenant($roomIdArray, $tenantId)
    {
        $state = false;

        $query = "select * from `room` where tenant_id = '$tenantId'";
        $result = $this->conn->query($query);

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if (in_array($row['room_id'], $roomIdArray)) {
                        $state = true;
                    }
                }
            }
        }
        return $state;
    }

    public function returnMyRoomIdArray($houseIdArray)
    {
        $myRoomIdArray = [];
        $query = "select * from `room`";
        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if (in_array($row['house_id'], $houseIdArray)) {
                    $myRoomIdArray[] = $row['room_id'];
                }
            }
        }

        return $myRoomIdArray;
    }

    public function returnMyAcquiredRoomIdArray($houseIdArray)
    {
        $myAcquiredRoomIdArray = [];
        $query = "select * from `room` where is_acquired = 1";
        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if (in_array($row['house_id'], $houseIdArray)) {
                    $myAcquiredRoomIdArray[] = $row['room_id'];
                }
            }
        }

        return $myAcquiredRoomIdArray;
    }

    public function roomOperation($task, $roomId){
        $query = "";
        if($task == 'suspend')
            $query = "update `room` set room_state = 0 where room_id = '$roomId'";
        elseif($task == 'verify')
            $query = "update `room` set room_state = 1 where room_id = '$roomId'";
        elseif($task == 'remove')
            $query = "delete from `room` where room_id = '$roomId'";
        elseif($task == "removeAllRooms")
            $query = "delete from `room` where house_id = '$roomId'";
        elseif($task == "remove-tenant")
            $query = "update `room` set is_acquired = 0, tenant_id = 0 where room_id = '$roomId'";

        $result = $this->conn->query($query);

        return $result?true:false;
    }

    public function updateRoomPhoto($oldRoomPhoto, $newRoomPhoto){
        $query = "update `room_photo` set room_photo = '$newRoomPhoto' where room_photo = '$oldRoomPhoto'";
        $result = $this->conn->query($query);
        return $result ? true: false;
    }
}