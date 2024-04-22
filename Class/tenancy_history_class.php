<?php
include_once 'connection_class.php';
class TenancyHistory extends DatabaseConnection
{
    public $tenancyHistoryId;
    public $roomId;
    public $tenantId;
    public $moveInDate;
    public $moveOutDate;

    public function setTenancyHistory($roomId, $tenantId, $moveInDate, $moveOutDate)
    {
        $this->roomId = $roomId;
        $this->tenantId = $tenantId;
        $this->moveInDate = $moveInDate;
        $this->moveOutDate = $moveOutDate;
    }

    public function register()
    {
        $query = "insert into `tenancy_history` (room_id, tenant_id, move_in_date, move_out_date) 
                                values('$this->roomId', '$this->tenantId', '$this->moveInDate', '$this->moveOutDate')";

        $response = mysqli_query($this->conn, $query);
        return $response ? $this->getImmediateTenancyHistoryId() : false;
    }

    private function getImmediateTenancyHistoryId()
    {
        $query = "select tenancy_history_id from `tenancy_history` where room_id = '$this->roomId' and tenant_id = '$this->tenantId' and move_in_date = '$this->moveInDate'";
        $result = $this->conn->query($query);

        $tenancyHistoryId = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $tenancyHistoryId = $row['tenancy_history_id'];
        }

        return $tenancyHistoryId;
    }

    // returning all tenancy history
    function fetchTenancyHistoryOfRoom($roomId)
    {
        $query = "select * from `tenancy_history` where room_id = '$roomId' order by tenancy_history_id desc";

        $result = $this->conn->query($query);

        $tenancyHistoryArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $tenancyHistoryArray[] = $row;

        return $tenancyHistoryArray;
    }

    function fetchTenancyHistoryOfTenant($tenantId)
    {
        $query = "select * from `tenancy_history` where tenant_id = '$tenantId' order by tenancy_history_id desc";

        $result = $this->conn->query($query);

        $tenancyHistoryArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $tenancyHistoryArray[] = $row;

        return $tenancyHistoryArray;
    }

    function fetchTenancyHistory($tenantId, $roomId)
    {
        $query = "select * from `tenancy_history` where tenant_id = '$tenantId' and room_id = '$roomId' order by tenancy_history_id desc";

        $result = $this->conn->query($query);

        $tenancyHistoryArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $tenancyHistoryArray[] = $row;

        return $tenancyHistoryArray;
    }

    function fetchTenancyHistoryForLandlord($roomIdArray)
    {
        $query = "select * from `tenancy_history` order by tenancy_history_id desc";

        $result = $this->conn->query($query);

        $tenancyHistoryArray = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (in_array($row['room_id'], $roomIdArray)) {
                    $tenancyHistoryArray[] = $row;
                }
            }
        }

        return $tenancyHistoryArray;
    }

    public function updateTenancyHistory($roomId, $tenantId, $moveOutDate){
        $query = "update `tenancy_history` set move_out_date = '$moveOutDate' where room_id = '$roomId' and tenant_id = '$tenantId'";
        $result = $this->conn->query($query);
        return $result ? true : false;
    }
}