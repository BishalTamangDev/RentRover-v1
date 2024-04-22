<?php
include_once 'connection_class.php';

class LeaveApplication extends DatabaseConnection
{
    public $leaveApplicationId;
    public $roomId;
    public $landlordId;
    public $tenantId;
    public $leaveDate;
    public $note;
    public $state; // pending, accepted, rejected, cancelled, re-applied
    public $leaveApplicationDate;

    public function setLeaveApplication($roomId, $landlordId, $tenantId, $leaveDate, $note, $state, $leaveApplicationDate)
    {
        $this->roomId = $roomId;
        $this->landlordId = $landlordId;
        $this->tenantId = $tenantId;
        $this->leaveDate = $leaveDate;
        $this->note = $this->conn->real_escape_string($note);
        $this->state = $state;
        $this->leaveApplicationDate = $leaveApplicationDate;
    }

    public function registerLeaveApplication()
    {
        $query = "insert into `leave_application` (room_id, landlord_id, tenant_id, leave_date, note, state, application_date) 
                values('$this->roomId','$this->landlordId', '$this->tenantId', '$this->leaveDate','$this->note','$this->state', '$this->leaveApplicationDate')";

        $response = mysqli_query($this->conn, $query);

        return $response ? $this->getImmediateLeaveApplicationId() : false;
    }

    private function getImmediateLeaveApplicationId()
    {
        $query = "select leave_application_id from `leave_application` where room_id = '$this->roomId' and tenant_id = '$this->tenantId' and application_date = '$this->leaveApplicationDate'";
        $result = mysqli_query($this->conn, $query);

        $leaveApplicationId = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $leaveApplicationId = $row['leave_application_id'];
        }

        return $leaveApplicationId;
    }

    public function getLeaveApplicationId($roomId, $tenantId)
    {
        $query = "select leave_application_id from `leave_application` where room_id = '$roomId' and tenant_id = '$tenantId'";
        $result = mysqli_query($this->conn, $query);

        $leaveApplicationId = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $leaveApplicationId = $row['leave_application_id'];
        }

        return $leaveApplicationId;
    }

    function fetchLeaveApplication($leaveApplicationId)
    {

        $query = "select * from `leave_application` where leave_application_id = '$leaveApplicationId'";
        $result = $this->conn->query($query);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->setLeaveApplication($row['room_id'], $row['landlord_id'], $row['tenant_id'], $row['leave_date'], $row['note'], $row['state'], $row['application_date']);
            }
        }
    }

    function fetchAllLeaveApplications($roomId)
    {
        $query = "select * from `leave_application` where state = 0 and room_id = '$roomId' order by leave_application_id desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return $applications;
    }

    function fetchLeaveApplicationsOfRoom($roomId)
    {
        $query = "select * from `leave_application` where room_id = '$roomId' order by leave_application_id desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return $applications;
    }

    function getPendingLeaveApplicationForThisRoom($roomId)
    {
        $query = "select leave_application_id from `leave_application` where room_id = '$roomId' and state = 0";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row['leave_application_id'];

        return $applications;
    }

    function getPendingLeaveApplicantIdForThisRoom($roomId)
    {
        $query = "select tenant_id from `leave_application` where room_id = '$roomId' and state = 0";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row['tenant_id'];

        return $applications;
    }

    function fetchAllLeaveApplicationsForTenant($tenantId)
    {
        $query = "select * from `leave_application` where tenant_id = '$tenantId' order by leave_application_id desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return $applications;
    }

    public function fetchLeaveApplicationsForLandlord($roomIdArray)
    {
        $query = "select * from `leave_application` order by leave_application_id desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (in_array($row['room_id'], $roomIdArray)) {
                    $applications[] = $row;
                }
            }
        }

        return $applications;
    }

    public function countRoomLeaveApplication($roomId, $which)
    {
        if ($which == "pending")
            $query = "select * from `leave_application` where room_id = '$roomId' and state = 0 order by leave_application_id desc";
        elseif ($which == "accepted")
            $query = "select * from `leave_application` where room_id = '$roomId' and state = 1 order by leave_application_id desc";
        elseif ($which == "rejected")
            $query = "select * from `leave_application` where room_id = '$roomId' and state = 2 order by leave_application_id desc";
        else
            $query = "select * from `leave_application` where room_id = '$roomId' order by leave_application_id desc";

        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return sizeof($applications);
    }

    public function countLeaveApplicationType($applicationIdArray, $which)
    {
        $count = 0;
        $query = "select * from `leave_application`";
        $result = $this->conn->query($query);

        if ($which == "pending")
            $state = 0;
        elseif ($which == "accepted")
            $state = 1;
        elseif ($which == "rejected")
            $state = 2;
        elseif ($which == "tenant")
            $state = 6;
        else
            $state = 10;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['state'] == $state) {
                    $count++;
                }

                if ($state == 1) {
                    if ($row['state'] == 6) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    function countLeaveApplicationForTenant($tenantId, $which)
    {
        if ($which == "pending")
            $query = "select * from `leave_application` where tenant_id = '$tenantId' and state = 0 order by leave_application_id desc";
        elseif ($which == "accepted")
            $query = "select * from `leave_application` where tenant_id = '$tenantId' and state = 1 order by leave_application_id desc";
        elseif ($which == "rejected")
            $query = "select * from `leave_application` where tenant_id = '$tenantId' and state = 2 order by leave_application_id desc";
        else
            $query = "select * from `leave_application` where tenant_id = '$tenantId' order by leave_application_id desc";

        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return sizeof($applications);
    }

    // accepted || rejected
    public function getLeaveApplicationState($roomId, $tenantId)
    {
        $query = "select state from `leave_application` where room_id = '$roomId' and tenant_id = '$tenantId' order by leave_application_id desc limit 1";
        $result = $this->conn->query($query);
        if ($result->num_rows == 0) {
            return -1;
        }

        if ($result->num_rows == 0)
            return 0;
        else
            return $result->fetch_assoc()['state'];
    }

    public function leaveApplicationOperation($task, $roomId, $tenantId)
    {
        // state : pending, accepted

        $query = "";

        if ($task == "accept")
            $query = "update `leave_application` set state = 1 where room_id = '$roomId' and tenant_id = '$tenantId'";

        return ($this->conn->query($query)) ? true : false;
    }


    // public function leaveApplicationValidityCheck($landlordId, $applicantId, $roomId, $applicationId){
    //     $query = "select leave_application_id from `leave_application` where landlord_id = '$landlordId' and tenant_id = '$applicantId' and room_id =  '$roomId' and leave_application_id = '$applicationId'";
    //     $result = $this->conn->query($query);

    //     return ($result->num_rows != 0)?true:false;
    // }
}