<!-- all the notification class -->
<?php
include_once 'connection_class.php';

class Notification extends DatabaseConnection
{
    public $notificationId;
    public $whose;     // admin || tenant || landlord
    public $type;
    public $tenantId;
    public $landlordId;
    public $houseId;
    public $roomId;
    public $userVoiceId;
    public $announcementId;
    public $announcementResponseId;
    public $reviewId;
    public $applicationId;
    public $leaveApplicationId;
    public $tenantVoiceId;
    public $tenantVoiceResponseId;

    public $seen;
    public $dateTime;


    // notification types

    // new-user
    // user-verify
    // user-reverify
    // user-suspend

    // new-house
    // house-verify
    // house-reverify
    // house-suspend

    // new-room
    // room-verify
    // room-reverify
    // room-suspend

    // announcement
    // announcement-response

    // feedback
    // feedback-response

    // review
    // review-response

    // room-apply
    // room-apply-acceptance


    // user
    public function setUserNotification($typeId, $whose, $userId, $role)
    {
        $this->whose = $this->conn->real_escape_string($whose);
        $this->dateTime = date('Y-m-d H:i:s');

        if ($typeId == 0)
            $this->type = "user-registration";
        elseif ($typeId == 1)
            $this->type = "user-verify";
        elseif ($typeId == 2)
            $this->type = "user-suspend";

        // temp
        elseif ($typeId == 3)
            $this->type = "user-reverify";


        if ($role == "tenant")
            $this->tenantId = $userId;
        else
            $this->landlordId = $userId;
    }

    public function resetObject(){
        $this->tenantId = 0;
        $this->landlordId = 0;
        $this->houseId = 0;
        $this->roomId = 0;
        $this->userVoiceId = 0;
        $this->announcementId = 0;
        $this->reviewId = 0;
        $this->seen = false;
        $this->applicationId = 0;
        // return true;
    }

    public function register()
    {
        $query = "insert into `notification` (whose, type, tenant_id, landlord_id, house_id, room_id, feedback_id, announcement_id, announcement_response_id, review_id, application_id, leave_application_id, tenant_voice_id, tenant_voice_response_id, seen, date_time) 
        values ('$this->whose','$this->type','$this->tenantId','$this->landlordId','$this->houseId','$this->roomId','$this->userVoiceId','$this->announcementId','$this->announcementResponseId','$this->reviewId','$this->applicationId','$this->leaveApplicationId','$this->tenantVoiceId','$this->tenantVoiceResponseId','$this->seen', '$this->dateTime')";

        $response = mysqli_query($this->conn, $query);

        return ($response) ? true : false;
    }

    // house
    public function setHouseNotification($typeId, $whose, $landlordId, $houseId)
    {
        $this->whose = $this->conn->real_escape_string($whose);
        $this->houseId = $houseId;
        $this->landlordId = $landlordId;
        $this->dateTime = date('Y-m-d H:i:s');

        if ($typeId == 0)
            $this->type = "house-registration";
        elseif ($typeId == 1)
            $this->type = "house-verify";
        elseif ($typeId == 2)
            $this->type = "house-suspend";
        elseif ($typeId == 3)
            $this->type = "house-reverify";
    }

    // room
    public function setRoomNotification($typeId, $whose, $landlordId, $roomId)
    {
        $this->whose = $this->conn->real_escape_string($whose);
        $this->roomId = $roomId;
        $this->landlordId = $landlordId;
        $this->dateTime = date('Y-m-d H:i:s');

        if ($typeId == 0)
            $this->type = "room-registration";
        elseif ($typeId == 1)
            $this->type = "room-verify";
        elseif ($typeId == 2)
            $this->type = "room-suspend";
        elseif ($typeId == 3)
            $this->type = "room-reverify";
    }

    // user voice
    public function setUserVoiceNotification($type, $whose, $userId, $role, $immediateId)
    {
        $this->type = $this->conn->real_escape_string($type);
        $this->whose = $this->conn->real_escape_string($whose);
        $this->userVoiceId = $this->conn->real_escape_string($immediateId);
        $this->dateTime = date('Y-m-d H:i:s');

        if ($role == "landlord")
            $this->landlordId = $this->conn->real_escape_string($userId);
        else
            $this->tenantId = $this->conn->real_escape_string($userId);
    }

    // room application
    public function setApplicationNotification($type, $roomId, $landlordId, $tenantId, $applicationId)
    {
        $this->type = "null";
        if ($type == "room-application-submit")
            $this->type = "room-application-submit";
        elseif ($type == "room-application-accept")
            $this->type = "room-application-accept";
        elseif ($type == "room-application-reject")
            $this->type = "room-application-reject";
        elseif ($type == "room-application-make-tenant")
            $this->type = "room-application-make-tenant";
        elseif ($type == "room-application-re-apply")
            $this->type = "room-application-re-apply";
        elseif ($type == "room-application-cancel")
            $this->type = "room-application-cancel";
        elseif ($type == "room-leave-application-accept")
            $this->type = "room-leave-application-accept";

        // $this->whose = "landlord";

        $this->landlordId = $landlordId;
        $this->tenantId = $tenantId;
        $this->roomId = $roomId;
        $this->applicationId = $applicationId;
        $this->dateTime = date('Y-m-d H:i:s');
    }

    // room review
    public function setRoomReviewNotification($reviewId, $roomId, $landlordId, $tenantId){
        $this->resetObject();
        $this->type = "room-review";
        $this->whose = "landlord";
        $this->tenantId = $tenantId;
        $this->reviewId = $reviewId;
        $this->landlordId = $landlordId;
        $this->roomId = $roomId;
        $this->dateTime = date('Y-m-d H:i:s');
    }


    // room leave aaplication
    public function setLeaveApplicationNotification($type, $roomId, $landlordId, $tenantId, $leaveApplicationId)
    {
        $this->type = "null";
        if ($type == "tenancy-end")
            $this->type = "tenancy-end";
        elseif($type == "room-leave-application-submit"){
            $this->type = "room-leave-application-submit";
        }

        $this->landlordId = $landlordId;
        $this->tenantId = $tenantId;
        $this->roomId = $roomId;
        $this->leaveApplicationId = $leaveApplicationId;
        $this->dateTime = date('Y-m-d H:i:s');
    }

    // custom room application
    public function setCustomRoomNotification($tenantId, $immediateRoomId, $date)
    {
        $this->whose = "tenant";
        $this->type = "custom-room-notification";
        $this->tenantId = $tenantId;
        $this->roomId = $immediateRoomId;
        $this->dateTime = $date;
    }

    // tenant voice notification
    public function setTenantVoiceNotification($type, $roomId, $tenantVoiceId, $landlordId, $tenantId)
    {
        $this->type = "none";

        if ($type == "tenant-voice-submit")
            $this->type = "tenant-voice-submit";
        elseif ($type == "tenant-voice-solved")
            $this->type = "tenant-voice-solved";
        
        $this->roomId = $roomId;
        $this->tenantVoiceId = $tenantVoiceId;
        $this->landlordId = $landlordId;
        $this->tenantId = $tenantId;
        $this->dateTime = date('Y-m-d H:i:s');
    }

    // tenant voice response notification
    public function setTenantVoiceResponseNotification($type, $roomId, $tenantVoiceId, $landlordId, $tenantId)
    {
        $this->type = "none";

        if ($type == "tenant-voice-response")
            $this->type = "tenant-voice-response";

        
        $this->roomId = $roomId;
        $this->tenantVoiceId = $tenantVoiceId;
        $this->landlordId = $landlordId;
        $this->tenantId = $tenantId;
        $this->dateTime = date('Y-m-d H:i:s');
    }

    // announcement notification
    public function setAnnouncementNotification($whose, $type, $landlordId, $houseId, $roomId, $tenantId, $announcementId){
        $this->whose = $whose;
        $this->type = $type;
        $this->landlordId = $landlordId;
        $this->houseId = $houseId;
        $this->roomId = $roomId;
        $this->tenantId = $tenantId;
        $this->announcementId = $announcementId;
        $this->dateTime = date('Y-m-d H:i:s');
    }

    // tenancy end notification
    public function setTenancyEndNotification($roomId, $tenantId){
        $this->whose = 'tenant';
        $this->type = 'tenancy-end';
        $this->tenantId = $tenantId;
        $this->roomId = $roomId;
        $this->dateTime = date('Y-m-d H:i:s');
    }

    // returning for users
    function fetchNotification($whose, $userId)
    {
        $whose = $this->conn->real_escape_string($whose);

        if ($whose == "admin")
            $query = "select * from `notification` where whose = '$whose' order by notification_id desc";
        else
            $query = ($whose == "tenant") ? "select * from `notification` where whose = 'tenant' and tenant_id = '$userId' order by notification_id desc" : "select * from `notification` where whose = 'landlord' and landlord_id = '$userId' order by notification_id desc";

        $result = $this->conn->query($query);

        $notificationList = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $notificationList[] = $row;

        return $notificationList;
    }

    public function countNotification($whose, $userId, $status)
    {
        $userId = $this->conn->real_escape_string($userId);

        if ($whose == "admin")
            $query = "select * from `notification` where whose ='admin' ";
        else
            $query = ($whose == "tenant") ? "select * from `notification` where whose ='tenant' and tenant_id = '$userId' " : "select * from `notification` where whose ='landlord' and landlord_id = '$userId' ";

        if ($status == "unseen")
            $query = $query . 'and seen = 0';
        elseif ($status == "seen")
            $query = $query . 'and seen = 1';

        $query = $query . ' order by notification_id desc';

        $result = mysqli_query($this->conn, $query);

        return mysqli_num_rows($result);
    }

    public function deleteNotification($notificationId)
    {
        $notificationId = $this->conn->real_escape_string($notificationId);

        $query = "delete from `notification` where notification_id = '$notificationId'";
        $result = mysqli_query($this->conn, $query);
    }
}
?>