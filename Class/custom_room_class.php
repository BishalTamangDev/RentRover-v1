<?php
include_once 'connection_class.php';

class CustomRoomApplication extends DatabaseConnection
{
    public $customRoomApplicationId;
    public $tenantId;
    public $district;
    public $areaName;
    public $roomType;
    public $minRent;
    public $maxRent;
    public $furnishing;
    public $state;
    public $date;


    public function setCustomRoomApplication($tenantId, $district, $areaName, $roomType, $minRent, $maxRent, $furnishing, $state, $date)
    {
        $this->tenantId = $tenantId;
        $this->district = $district;
        $this->areaName = $areaName;
        $this->roomType = $roomType;
        $this->minRent = $minRent;
        $this->maxRent = $maxRent;
        $this->furnishing = $furnishing;
        $this->state = $state;
        $this->date = $date;
    }

    public function registerCustomRoomApplication()
    {
        $query = "insert into `custom_room` (tenant_id, district, area_name, room_type, min_rent, max_rent, furnishing, state, date) 
                values('$this->tenantId', '$this->district', '$this->areaName', '$this->roomType', '$this->minRent', '$this->maxRent', '$this->furnishing', '$this->state', '$this->date')";

        $response = mysqli_query($this->conn, $query);

        return $response ? $this->getImmediateApplicationId() : false;
    }

    private function getImmediateApplicationId()
    {
        $query = "select custom_room_id from `custom_room` where tenant_id = '$this->tenantId' and date = '$this->date'";
        $result = mysqli_query($this->conn, $query);

        $customRoomApplicationId = 0;

        $customRoomApplicationIdArray = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc())
                $customRoomApplicationIdArray[] = $row;

            foreach ($customRoomApplicationIdArray as $temp)
                $customRoomApplicationId = $temp['custom_room_application_id'];
        }

        return $customRoomApplicationId;
    }

    public function countCustomRoomApplication($type)
    {
        $type = ($type == "unserved") ? 0 : 1;
        $query = "select custom_room_id  from `custom_room` where state = '$type'";
        $response = mysqli_query($this->conn, $query);
        $count = mysqli_num_rows($response);
        return $count;
    }

    public function fetchCustomRoomApplication($customRoomApplicationId)
    {
        $query = "select * from `custom_room` where custom_room_id  = '$customRoomApplicationId'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);
        $this->setCustomRoomApplication($row['tenant_id'], $row['district'], $row['area_name'], $row['room_type'], $row['min_rent'], $row['max_rent'], $row['furnishing'], $row['state'], $row['date']);
    }

    public function fetchAllCustomRoomApplication()
    {
        $query = "select * from `custom_room` order by custom_room_id desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return $applications;
    }

    public function fetchAllCustomRoomApplicationTenant($tenantId)
    {
        $query = "select * from `custom_room` where tenant_id = '$tenantId' order by custom_room_id  desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return $applications;
    }


    public function checkForCustomRoomApplication($district, $areaName, $roomType, $rent, $furnishing)
    {
        $query = "select tenant_id from `custom_room` where district = '$district' and area_name = '$areaName' and room_type = '$roomType' and furnishing = '$furnishing' and min_rent <= '$rent' and max_rent >= '$rent'";
        $result = $this->conn->query($query);
        $tenantIdArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $tenantIdArray[] = $row['tenant_id'];

        return $tenantIdArray;
    }

    public function countCustomApplication($which, $tenantId)
    {
        $query = "select * from `custom_room`";
        if ($which == "served")
            $query = $query . "where state = 1";
        elseif ($which == "unserved")
            $query = $query . "where state = 0";

        $result = $this->conn->query($query);
        $count = 0;

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                if ($row['tenant_id'] == $tenantId)
                    $count++;

        return $count;
    }
}