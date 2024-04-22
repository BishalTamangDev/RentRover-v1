<?php
include_once 'connection_class.php';
class TenantVoice extends DatabaseConnection
{
    public $tenantVoiceId;
    public $tenantId;
    public $roomId;
    public $voice;
    public $date;
    public $issueState;
    public $issueSolvedDate;

    // set up tenant voice
    public function setTenantVoice($tenantId, $roomId, $voice, $date, $issueState)
    {
        $this->tenantId = $tenantId;
        $this->roomId = $roomId;
        $this->date = $date;
        $this->issueState = $issueState;
        $this->voice = $this->conn->real_escape_string($voice);
    }

    public function registerTenantVoice()
    {
        $query = "insert into `tenant_voice` (room_id, tenant_id, voice, date, issue_state) 
                                values('$this->roomId', '$this->tenantId', '$this->voice', '$this->date', '$this->issueState')";

        $response = mysqli_query($this->conn, $query);

        return $response ? $this->getImmediateTenantVoiceId() : false;
    }

    public function getImmediateTenantVoiceId()
    {
        $tenantVoiceId = 0;
        $query = "select tenant_voice_id from `tenant_voice` where room_id = '$this->roomId' and tenant_id = '$this->tenantId' and date = '$this->date'";
        $result = $this->conn->query($query);

        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $tenantVoiceId = $row['tenant_voice_id'];
        }

        return $tenantVoiceId;
    }

    public function solveTenantVoice($tenantVoiceId)
    {
        $this->issueSolvedDate = date('Y-m-d H-i-s');
        $query = "update `tenant_voice` set issue_state = 1, issue_solved_date = '$this->issueSolvedDate' where tenant_voice_id = $tenantVoiceId";
        $result = $this->conn->query($query);

        return $result ? true : false;
    }

    function fetchAllTenantVoice($roomId)
    {
        $query = "select * from `tenant_voice` where room_id = '$roomId'";
        $result = $this->conn->query($query);

        $tenantVoiceArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $tenantVoiceArray[] = $row;

        return $tenantVoiceArray;
    }

    function fetchTenantVoice($voiceId)
    {
        $query = "select * from `tenant_voice` where tenant_voice_id = '$voiceId'";
        $result = $this->conn->query($query);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $this->setTenantVoice($row['tenant_id'], $row['room_id'], $row['voice'], $row['date'], $row['issue_state']);
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    public function fetchMyTenantVoice($tenantId)
    {
        $query = "select * from `tenant_voice` where tenant_id = '$tenantId' order by tenant_voice_id desc";
        $result = $this->conn->query($query);

        $myVoiceArray = [];

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc())
                    $myVoiceArray[] = $row;
            }
        }
        return $myVoiceArray;
    }

    public function fetchTenantVoiceForLandlord($roomIdArray)
    {
        $query = "select * from `tenant_voice` order by tenant_voice_id desc";
        $result = $this->conn->query($query);

        $tempTenantVoiceArray = [];
        $tenantVoiceArray = [];

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tempTenantVoiceArray[] = $row;
                }

                // filtering voices
                if (sizeof($tempTenantVoiceArray) > 0) {
                    foreach ($tempTenantVoiceArray as $tenantVoice) {
                        if (in_array($tenantVoice['room_id'], $roomIdArray)) {
                            $tenantVoiceArray[] = $tenantVoice;
                        }
                    }
                }
            }
        }

        return $tenantVoiceArray;
    }

    public function fetchLatestTenantVoiceForLandlord($roomIdArray)
    {
        $query = "select * from `tenant_voice` order by tenant_voice_id desc";
        $result = $this->conn->query($query);

        $tempTenantVoiceArray = [];
        $tenantVoiceArray = [];

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tempTenantVoiceArray[] = $row;
                }

                // filtering voices
                if (sizeof($tempTenantVoiceArray) > 0) {
                    foreach ($tempTenantVoiceArray as $tenantVoice) {
                        if (in_array($tenantVoice['room_id'], $roomIdArray)) {
                            $tenantVoiceArray[] = $tenantVoice;
                        }
                    }

                    if (sizeof($tenantVoiceArray) > 0) {
                        $this->tenantVoiceId = $tenantVoiceArray[0]['tenant_voice_id'];
                        $this->setTenantVoice($tenantVoiceArray[0]['tenant_id'], $tenantVoiceArray[0]['room_id'], $tenantVoiceArray[0]['voice'], $tenantVoiceArray[0]['date'], $tenantVoiceArray[0]['issue_state']);
                    }
                }
            }
        }
    }

    public function countTenantVoiceForLandlord($roomIdArray){
        $count = 0;
        $query = "select * from `tenant_voice`";
        $result = $this->conn->query($query);
        if($result){
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    if(in_array($row['room_id'], $roomIdArray)){
                        $count++;
                    }
                }
            }
        }
        return $count;
    }
}

class TenantVoiceResponse extends TenantVoice
{
    public $tenantVoiceResponseId;
    public $tenantVoiceId;
    public $roomId;
    public $tenantId;
    public $landlordId;
    public $response;
    public $whose;
    public $responseDate;

    public $db;

    // set up tenant voice
    public function setTenantVoiceResponse($roomId, $tenantVoiceId, $whose, $tenantId, $landlordId, $response, $responseDate)
    {
        $this->roomId = $roomId;
        $this->tenantVoiceId = $tenantVoiceId;
        $this->tenantId = $tenantId;
        $this->whose = $whose;
        $this->landlordId = $landlordId;
        $this->responseDate = $responseDate;
        $this->response = $this->conn->real_escape_string($response);
    }

    public function registerTenantVoiceResponse()
    {
        $query = "insert into `tenant_voice_response` (room_id, tenant_voice_id, whose, tenant_id, landlord_id, response, response_date) 
                                values('$this->roomId', '$this->tenantVoiceId', '$this->whose', '$this->tenantId', '$this->landlordId', '$this->response', '$this->responseDate')";

        $response = mysqli_query($this->conn, $query);

        return $response ? $this->getImmediateTenantVoiceResponseId() : false;
    }

    private function getImmediateTenantVoiceResponseId()
    {
        $query = "select tenant_voice_response_id from `tenant_voice_response` where room_id = '$this->roomId' and tenant_voice_id = '$this->tenantVoiceId' and landlord_id = '$this->landlordId' and tenant_id = '$this->tenantId' and response_date = '$this->responseDate'";
        $result = $this->conn->query($query);

        $tenantVoiceResponseId = 0;

        if($result){
            if ($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $tenantVoiceResponseId = $row['tenant_voice_response_id'];
            }

        }

        return $tenantVoiceResponseId;
    }

    function fetchAllTenantVoiceResponse($roomId, $tenantVoiceId)
    {
        $query = "select * from `tenant_voice_response` where room_id = '$roomId' and tenant_voice_id = '$tenantVoiceId' order by tenant_voice_response_id desc";
        $result = $this->conn->query($query);

        $tenantVoiceResponseArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $tenantVoiceResponseArray[] = $row;

        return $tenantVoiceResponseArray;
    }

    function fetchTenantVoiceResponse($voiceResponseId)
    {
        $query = "select * from `tenant_voice_response` where tenant_voice_response_id = '$voiceResponseId' order by tenant_voice_response_id desc";
        $result = $this->conn->query($query);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $this->setTenantVoiceResponse($row['room_id'], $row['tenant_voice_id'], $row['whose'], $row['tenant_id'], $row['landlord_id'], $row['response'], $row['response_date']);
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }


    public function fetchTenantVoiceResponseForLandlord($voiceId)
    {
        $query = "select * from `tenant_voice_response` where tenant_voice_id '$voiceId' order by tenant_voice_response_id desc";
        $result = $this->conn->query($query);

        $tenantVoiceResponseArray = [];

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc())
                    $tenantVoiceResponseArray[] = $row;

            }
        }

        return $tenantVoiceResponseArray;
    }

    public function tenantVoiceResponseOperation($voiceResponseId){
        $query = "delete from `tenant_voice_response` where tenant_voice_response_id = '$voiceResponseId'";
        $result = $this->conn->query($query);
        return ($result)? true:false;
    }
}