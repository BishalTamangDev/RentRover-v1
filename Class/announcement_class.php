<?php
include_once 'connection_class.php';
class Announcement extends DatabaseConnection
{
    public $announcementId;
    public $whose; // admin or landlord
    public $target;
    public $landlordId;
    public $tenantId;
    public $houseId;
    public $roomId;
    public $title;
    public $announcement;
    public $announcementDate;


    public function setAnnouncement($announcementId, $whose, $target, $landlordId, $tenantId, $houseId, $roomId, $title, $announcement, $announcementDate)
    {
        $this->whose = $this->conn->real_escape_string($whose);
        $this->target = $target;
        $this->landlordId = $landlordId;
        $this->tenantId = $tenantId;
        $this->houseId = $houseId;
        $this->roomId = $roomId;
        $this->title = $this->conn->real_escape_string($title);
        $this->announcement = $this->conn->real_escape_string($announcement);
        $this->announcementDate = $announcementDate;
    }

    public function registerAnnouncement()
    {
        $query = "insert into `announcement` (announcement_id, whose, target, landlord_id, tenant_id, house_id, room_id, title, announcement, announcement_date) 
                                values('$this->announcementId', '$this->whose', '$this->target', '$this->landlordId', '$this->tenantId', '$this->houseId', '$this->roomId', '$this->title', '$this->announcement', '$this->announcementDate')";

        $response = mysqli_query($this->conn, $query);
        return $response ? $this->getImmediateAnnouncementId() : false;
    }

    public function getImmediateAnnouncementId()
    {
        $query = "select announcement_id from `announcement` where title = '$this->title' and announcement = '$this->announcement' and announcement_date = '$this->announcementDate'";
        $result = $this->conn->query($query);

        $announcementId = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $announcementId = $row['announcement_id'];
        }

        return $announcementId;
    }

    // returning all suers
    function fetchAllAnnouncement($whose, $landlordId)
    {
        if ($whose == 'admin')
            $query = "select * from `announcement` where whose='admin' order by announcement_id desc";
        else if ($whose == 'landlord')
            $query = "select * from `announcement` where whose='landlord' and landlord_id = '$landlordId' order by announcement_id desc";
        else
            $query = "select * from `announcement` where whose='landlord' and tenant_id = '$landlordId' order by announcement_id desc";

        $result = $this->conn->query($query);

        $announcements = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $announcements[] = $row;

        return $announcements;
    }

    function fetchAllSystemAnnouncementForUser($role)
    {
        if ($role == 'tenant')
            $query = "select * from `announcement` where whose = 'admin' and target = 0 or target = 2 order by announcement_id desc";
        else
            $query = "select * from `announcement` where whose = 'admin' and target = 0 or target = 1 order by announcement_id desc";

        $result = $this->conn->query($query);

        $systemAnnouncements = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $systemAnnouncements[] = $row;

        return $systemAnnouncements;
    }

    public function countAnnouncement($whose, $landlordId, $target)
    {
        $query = "";
        $whose = $this->conn->real_escape_string($whose);

        if ($whose == 'landlord') {
            if ($target == 0)
                $query = "select announcement_id from `announcement` where whose = '$whose' and target = 0";
            elseif ($target == 1)
                $query = "select announcement_id from `announcement` where whose = '$whose' and target = 1";
            elseif ($target == 2)
                $query = "select announcement_id from `announcement` where whose = '$whose' and target = 2";
            elseif ($target == 3)
                $query = "select announcement_id from `announcement` where whose = '$whose' and target = 3";
            else
                $query = "select announcement_id from `announcement` where whose = '$whose'";
        } else {
            $query = "select announcement_id from `announcement` where whose = '$whose'";
        }

        if ($landlordId != 0) {
            $query = $query . " and landlord_id = '$landlordId'";
        } else {
            if ($target == "both")
                $query = $query . " and target = 0";
            else if ($target == "landlord")
                $query = $query . " and target = 1";
            else if ($target == "tenant")
                $query = $query . " and target = 2";
            else
                $query = $query . " and target = 0 or target = 1 or target = 2";
        }

        $result = mysqli_query($this->conn, $query);

        return mysqli_num_rows($result);
    }

    public function fetchAnnouncement($announcementId)
    {
        $query = "select * from `announcement` where announcement_id = '$announcementId'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);
        $this->setAnnouncement($row['announcement_id'], $row['whose'], $row['target'], $row['landlord_id'], $row['tenant_id'], $row['house_id'], $row['room_id'], $row['title'], $row['announcement'], $row['announcement_date']);
    }

    public function fetchLatestSystemAnnouncement($whose)
    {
        if ($whose == 'landlord')
            $query = "select * from `announcement` where whose = 'admin' and target = 0 or target = 1 order by announcement_id desc limit 1";
        else
            $query = "select * from `announcement` where whose = 'admin' and target = 0 or target = 2 order by announcement_id desc limit 1";

        $result = mysqli_query($this->conn, $query);

        if ($result->num_rows) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return 0;
        }
    }

    public function isValid($announcementId)
    {
        return false;
    }

    public function fetchLatestAnnouncement($whose)
    {
        $whose = $this->conn->real_escape_string($whose);

        $query = "select * from `announcement` where whose = '$whose' order by announcement_id desc limit 1";
        $result = mysqli_query($this->conn, $query);

        $announcements = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $announcements[] = $row;

        return $announcements;
    }

    public function deleteAnnouncement($announcementId)
    {
        $query = "delete from `announcement` where announcement_id = '$announcementId'";
        $result = mysqli_query($this->conn, $query);
        return $result?true:false;
    }
}

class AnnouncementResponse extends Announcement
{
    public $announcementResponseId;
    public $announcementId;
    public $userId;
    public $role;
    public $response;
    public $acknowledge;
    public $announcementResponseDate;

    public function setAnnouncementResponse($announcementId, $userId, $role, $response, $acknowledge, $announcementResponseDate)
    {
        $this->announcementId = $announcementId;
        $this->userId = $userId;
        $this->role = $this->conn->real_escape_string($role);
        $this->response = $this->conn->real_escape_string($response);
        $this->acknowledge = $acknowledge;
        $this->announcementResponseDate = $announcementResponseDate;
    }


    // residue?????????
    public function getKeyValueByEmail($key, $email)
    {
        $email = $this->conn->real_escape_string($email);

        if ($key == 'userPhoto') {
            $query = "select profile_pic from `user` where email='$email'";
            $response = mysqli_query($this->conn, $query);
            $row = mysqli_fetch_assoc($response);
            return $row['profile_pic'];
        }
    }

    public function registerAnnouncementResponse($url)
    {
        $query = "insert into `announcement_response` (announcement_id, user_id, role, response, acknowledge, announcement_response_date) 
                                values('$this->announcementId', '$this->userId', '$this->role','$this->response', '$this->acknowledge', '$this->announcementResponseDate')";

        $response = mysqli_query($this->conn, $query);
        ($response) ? header('location: ' . $url) : header('location: ' . $url);
    }

    // returning all suers
    function fetchAllAnnouncementResponse($announcementId)
    {
        $query = "select * from `announcement_response` where announcement_id = '$announcementId' order by announcement_response_id desc";
        $result = $this->conn->query($query);

        $announcementResponses = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $announcementResponses[] = $row;

        return $announcementResponses;
    }

    public function fetchAnnouncementResponse($announcementId)
    {
        $query = "select * from `announcement_response` where announcement_id = '$announcementId'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);
        $this->setAnnouncementResponse($row['announcement_id'], $row['user_id'], $row['role'], $row['response'], $row['acknowledge'], $row['announcementResponseDate']);
    }

    public function deleteAnnouncementResponse($announcementResponseId, $url)
    {
        $query = "delete from `announcement_response` where announcement__response_id = '$announcementResponseId'";
        $response = mysqli_query($this->conn, $query);
        header('location: ' . $url);
    }

    public function deleteAnnouncementResponseByParent($announcementId)
    {
        $query = "delete from `announcement_response` where announcement_id = '$announcementId'";
        $response = mysqli_query($this->conn, $query);
    }

    public function announcementResponseOperation($announcementResponseId, $task)
    {
        if ($task == "remove") {
            $query = "delete from `announcement_response` where announcement_response_id = $announcementResponseId";
            $result = $this->conn->query($query);
            return ($result) ? true : false;
        }
    }
}