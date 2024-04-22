<?php
include_once 'connection_class.php';

class Application extends DatabaseConnection
{
    public $applicationId;
    public $roomId;
    public $landlordId;
    public $tenantId;
    public $rentType;
    public $moveInDate;
    public $moveOutDate;
    public $note;
    public $state; // pending, accepted, rejected, cancelled, re-applied
    public $applicationDate;

    public $cancelCount;
    public $applyCount;

    public function setApplication($roomId, $landlordId, $tenantId, $rentType, $moveInDate, $moveOutDate, $note, $state, $cancelCount, $applyCount, $applicationDate)
    {
        $this->roomId = $roomId;
        $this->landlordId = $landlordId;
        $this->tenantId = $tenantId;
        $this->rentType = $rentType;
        $this->moveInDate = $moveInDate;
        $this->moveOutDate = $moveOutDate;
        $this->note = $this->conn->real_escape_string($note);
        $this->state = $state;
        $this->cancelCount = $cancelCount;
        $this->applyCount = $applyCount;
        $this->applyCount = $applyCount;
        $this->applicationDate = $applicationDate;
    }

    public function registerApplication()
    {
        $query = "insert into `application` (room_id, landlord_id, tenant_id, rent_type, move_in_date, move_out_date, note, state, cancel_count, apply_count, application_date) 
                values('$this->roomId','$this->landlordId','$this->tenantId','$this->rentType','$this->moveInDate', '$this->moveOutDate', '$this->note', '$this->state', '$this->cancelCount', '$this->applyCount', '$this->applicationDate')";

        $response = mysqli_query($this->conn, $query);

        return $response ? $this->getImmediateApplicationId() : false;
    }

    public function getImmediateApplicationId()
    {
        $query = "select application_id from `application` where room_id = '$this->roomId' and tenant_id = '$this->tenantId' and application_date = '$this->applicationDate'";
        $result = mysqli_query($this->conn, $query);

        $applicationId = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $applicationId = $row['application_id'];
        }

        return $applicationId;
    }

    public function getApplicationId($roomId, $tenantId)
    {
        $query = "select application_id from `application` where room_id = '$roomId' and tenant_id = '$tenantId'";
        $result = mysqli_query($this->conn, $query);

        $applicationId = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $applicationId = $row['application_id'];
        }

        return $applicationId;
    }

    function fetchApplication($applicationId)
    {
        $query = "select * from `application` where application_id = '$applicationId'";
        $result = $this->conn->query($query);


        if ($result->num_rows > 0) {
            $row = mysqli_fetch_assoc($result);
            $this->setApplication($row['room_id'], $row['landlord_id'], $row['tenant_id'], $row['rent_type'], $row['move_in_date'], $row['move_out_date'], $row['note'], $row['state'], $row['cancel_count'], $row['apply_count'], $row['application_date']);
        }
    }

    function fetchAllApplications($roomId)
    {
        $query = "select * from `application` where state = 0 and room_id = '$roomId' order by application_id desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return $applications;
    }

    function fetchApplicationsOfRoom($roomId)
    {
        $query = "select * from `application` where room_id = '$roomId' order by application_id desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return $applications;
    }

    function getPendingApplicationForThisRoom($roomId)
    {
        $query = "select application_id from `application` where room_id = '$roomId' and state = 0";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row['application_id'];

        return $applications;
    }

    function getPendingApplicantIdForThisRoom($roomId)
    {
        $query = "select tenant_id from `application` where room_id = '$roomId' and state = 0";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row['tenant_id'];

        return $applications;
    }

    function fetchAllApplicationsForTenant($tenantId)
    {
        $query = "select * from `application` where tenant_id = '$tenantId' order by application_id desc";
        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return $applications;
    }

    public function fetchApplicationsForLandlord($roomIdArray)
    {
        $query = "select * from `application` order by application_id desc";
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

    public function countRoomApplication($roomId, $which)
    {
        if ($which == "pending")
            $query = "select * from `application` where room_id = '$roomId' and state = 0 order by application_id desc";
        elseif ($which == "accepted")
            $query = "select * from `application` where room_id = '$roomId' and state = 1 order by application_id desc";
        elseif ($which == "rejected")
            $query = "select * from `application` where room_id = '$roomId' and state = 2 order by application_id desc";
        else
            $query = "select * from `application` where room_id = '$roomId' order by application_id desc";

        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return sizeof($applications);
    }

    public function countApplicationType($applicationIdArray, $which)
    {
        $count = 0;
        $query = "select * from `application`";
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

    function countApplicationForTenant($tenantId, $which)
    {
        if ($which == "pending")
            $query = "select * from `application` where tenant_id = '$tenantId' and state = 0 order by application_id desc";
        elseif ($which == "accepted")
            $query = "select * from `application` where tenant_id = '$tenantId' and state = 1 order by application_id desc";
        elseif ($which == "rejected")
            $query = "select * from `application` where tenant_id = '$tenantId' and state = 2 order by application_id desc";
        else
            $query = "select * from `application` where tenant_id = '$tenantId' order by application_id desc";

        $result = $this->conn->query($query);

        $applications = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $applications[] = $row;

        return sizeof($applications);
    }

    // accepted || rejected
    public function getApplicationState($roomId, $tenantId)
    {
        $query = "select state from `application` where room_id = '$roomId' and tenant_id = '$tenantId' order by application_id desc limit 1";
        $result = $this->conn->query($query);
        if ($result->num_rows == 0) {
            return -1;
        }

        if ($result->num_rows == 0)
            return 0;
        else {
            return $result->fetch_assoc()['state'];
        }
    }

    public function applicationOperation($task, $roomId, $tenantId)
    {
        $this->state; // pending, accepted, rejected, cancelled, re-applied, leave-room

        $query = "";

        if ($task == "accept")
            $query = "update `application` set state = 1 where room_id = '$roomId' and tenant_id = '$tenantId'";
        elseif ($task == "reject")
            $query = "update `application` set state = 2 where room_id = '$roomId' and tenant_id = '$tenantId'";
        elseif ($task == "cancel")
            $query = "update `application` set state = 3, cancel_count = cancel_count + 1 where room_id = '$roomId' and tenant_id = '$tenantId'";
        elseif ($task == "leave-room")
            $query = "update `application` set state = 5 where room_id = '$roomId' and tenant_id = '$tenantId'";
        elseif ($task == "make-tenant")
            $query = "update `application` set state = 6 where room_id = '$roomId' and tenant_id = '$tenantId'";
        elseif ($task == "re-apply")
            $query = "update `application` set state = 0, apply_count = apply_count + 1 where room_id = '$roomId' and tenant_id = '$tenantId'";

        return ($this->conn->query($query)) ? true : false;
    }

    public function rejectRemainingApplication($roomId, $applicantId)
    {
        $query = "update `application` set state = 2 where room_id = '$roomId' and tenant_id != '$applicantId'";
        return ($this->conn->query($query)) ? true : false;
    }



    public function applicationValidityCheck($landlordId, $applicantId, $roomId, $applicationId)
    {
        $query = "select application_id from `application` where landlord_id = '$landlordId' and tenant_id = '$applicantId' and room_id =  '$roomId' and application_id = '$applicationId'";
        $result = $this->conn->query($query);

        return ($result->num_rows != 0) ? true : false;
    }
}