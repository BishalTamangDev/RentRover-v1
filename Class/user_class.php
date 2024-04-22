<?php
include_once 'connection_class.php';
class User extends DatabaseConnection
{
    public $userId;
    public $firstName;
    public $middleName;
    public $lastName;
    public $email;
    public $password;
    public $contact;
    public $gender;
    public $citizenshipNumber;
    public $citizenshipFrontPhoto;
    public $citizenshipBackPhoto;
    public $userPhoto;
    public $province;
    public $district;
    public $isVdc;
    public $areaName;
    public $wardNumber;
    public $role;
    public $dob;
    public $accountState;
    public $registerDate;


    public function setUser($firstName, $middleName, $lastName, $gender, $dob, $email, $password, $contact, $province, $district, $isVdc, $areaName, $wardNumber, $role, $userPhoto, $citizenshipNumber, $citizenshipFrontPhoto, $citizenshipBackPhoto, $accountState, $registerDate)
    {
        $this->firstName = $this->conn->real_escape_string($firstName);
        $this->middleName = $this->conn->real_escape_string($middleName);
        $this->lastName = $this->conn->real_escape_string($lastName);
        $this->gender = $this->conn->real_escape_string($gender);
        $this->dob = $dob;
        $this->email = $this->conn->real_escape_string($email);
        $this->password = $password;
        $this->contact = $contact;
        $this->province = $province;
        $this->district = $district;
        $this->isVdc = $isVdc;
        $this->areaName = $this->conn->real_escape_string($areaName);
        $this->wardNumber = $wardNumber;
        $this->role = $this->conn->real_escape_string($role);
        $this->userPhoto = $userPhoto;
        $this->citizenshipNumber = $citizenshipNumber;
        $this->citizenshipFrontPhoto = $citizenshipFrontPhoto;
        $this->citizenshipBackPhoto = $citizenshipBackPhoto;
        $this->accountState = $accountState;
        $this->registerDate = $registerDate;
    }

    // public function getKeyValueByEmail($key, $email)
    // {
    //     $key = $this->conn->real_escape_string($key);
    //     $email = $this->conn->real_escape_string($email);
        
    //     if ($key == 'userPhoto') {
    //         $query = "select profile_pic from `user` where email='$email'";
    //         $response = mysqli_query($this->conn, $query);
    //         $row = mysqli_fetch_assoc($response);
    //         return $row['profile_pic'];
    //     }
    // }

    public function register()
    {
        $query = "insert into `user` (first_name, middle_name, last_name, gender, dob, email, password, contact, province, district, isVdc, area_name, ward, role, profile_pic, citizenship_number, citizenship_front_pic, citizenship_back_pic, account_state, register_date) 
                                values('$this->firstName', '$this->middleName', '$this->lastName', '$this->gender', '$this->dob', '$this->email', '$this->password', '$this->contact', '$this->province', '$this->district', '$this->isVdc', '$this->areaName', '$this->wardNumber', '$this->role', '$this->userPhoto', '$this->citizenshipNumber', '$this->citizenshipFrontPhoto', '$this->citizenshipBackPhoto', '$this->accountState', '$this->registerDate')";

        $response = mysqli_query($this->conn, $query);

        return $response ? $this->getImmediateId() : false;
    }

    private function getImmediateId()
    {
        $query = "select user_id from `user` where email = '$this->email' and contact = '$this->contact' and citizenship_number = '$this->citizenshipNumber'";
        $result = $this->conn->query($query);
        $userIdArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $userIdArray[] = $row;

        foreach ($userIdArray as $temp)
            $userId = $temp['user_id'];

        return $userId;
    }

    public function validateEmail($email)
    {
         $query = "select * from `user` where email = '$email'";
        $response = mysqli_query($this->conn, $query);

        if (mysqli_num_rows($response) > 0) {
            // setting email address
            $this->email = $email;
            return true;
        }
    }

    // returning the id of a user 
    public function getUserId($email)
    {
        $query = "select user_id from `user` where email = '$email'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);
        return $row['user_id'];
    }

    public function getUserPhoto($userId){
        $query = "select profile_pic from `user` where user_id = '$userId'";
        $response = mysqli_query($this->conn, $query);

        if($response){
            if($response->num_rows > 0){
                $row = mysqli_fetch_assoc($response);
                return $row['profile_pic'];
            }else{
                return "blank.jpg";
            }
        }else{
            return "blank.jpg";
        }
    }

    public function getRole($userId)
    {
        $role = "Unknown";
        $query = "select role from `user` where user_id = '$userId'";
        $response = mysqli_query($this->conn, $query);
        
        if($response){
            $row = mysqli_fetch_assoc($response);
            $role = $row['role'];
        }
        
        return $role;
    }

    public function getUserName($userId)
    {
        $query = "select first_name, middle_name, last_name from `user` where user_id = '$userId'";
        $response = mysqli_query($this->conn, $query);

        if($response){
            if($response->num_rows > 0){
                $row = mysqli_fetch_assoc($response);
                return returnFormattedName($row['first_name'], $row['middle_name'], $row['last_name']);
            }else{
                return "Unknown";
            }
        }else{
            return "Unknown";
        }
        // return $response? returnFormattedName($row['first_name'], $row['middle_name'], $row['last_name']) : "Unknown";
    }

    public function getUserEmail($id)
    {
        $query = "select email from `user` where user_id = '$id'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);
        return $row['email'];
    }
    
    public function getUserAddress($id)
    {
        $query = "select ward, area_name, district, province from `user` where user_id = '$id'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);
        return returnFormattedAddress($row['province'], $row['district'], $row['area_name'], $row['ward']);
    }


    public function countUsers($role, $accountState)
    {
        $role = $this->conn->real_escape_string($role);
        
        if ($role == 'landlord')
            $query = "select * from `user` where role='landlord'";
        elseif ($role == 'tenant')
            $query = "select * from `user` where role='tenant'";
        else
            $query = "select * from `user`";


        if ($role == 'all') {
            if ($accountState != 'all')
                $query = $query . ' where ';
        } else {
            if ($accountState != 'all')
                $query = $query . ' and ';
        }


        if ($accountState == 'unverified')
            $query = $query . 'account_state = 0';
        elseif ($accountState == 'verified')
            $query = $query . 'account_state = 1';
        elseif ($accountState == 'suspended')
            $query = $query . 'account_state = 2';

        $response = mysqli_query($this->conn, $query);
        $count = mysqli_num_rows($response);
        return $count;
    }

    public function accountState($userId)
    {
        $query = "select account_state from `user` where user_id='$userId'";
        $response = mysqli_query($this->conn, $query);
        $count = mysqli_num_rows($response);
        if ($count > 0) {
            $row = mysqli_fetch_assoc($response);
            return $row['account_state'];
        } else
            return "Unknown";
    }

    // returning all suers
    function fetchAllUsers()
    {
        $query = "select * from `user`";
        $result = $this->conn->query($query);

        $users = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $users[] = $row;

        return $users;
    }

    function searchUser($content)
    {
        $content = $this->conn->real_escape_string($content);

        $provinceInteger = returnArrayIndex("province", returnFormattedString($content));
        $districtInteger = returnArrayIndex("district", returnFormattedString($content));

        $query = "select * from `user` where first_name like '$content' or middle_name like '$content' or last_name like '$content' or province like '$provinceInteger' or district like '$districtInteger' or area_name like '$content' or email like '$content' or contact like '$content'";

        $result = $this->conn->query($query);

        $users = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        } else {

        }
        return $users;
    }

    public function fetchSpecificRow($userId)
    {
        $state = true;
        $query = "select * from `user` where user_id = '$userId'";
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            if ($result->num_rows > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->setUser($row['first_name'], $row['middle_name'], $row['last_name'], $row['gender'], $row['dob'], $row['email'], $row['password'], $row['contact'], $row['province'], $row['district'], $row['isVdc'], $row['area_name'], $row['ward'], $row['role'], $row['profile_pic'], $row['citizenship_number'], $row['citizenship_front_pic'], $row['citizenship_back_pic'], $row['account_state'], $row['register_date']);
            } else {
                $state = false;
            }
        } else {
            $state = false;
        }
        return $state;
    }

    public function fetchUser($userId)
    {
        $query = "select * from `user` where user_id = '$userId'";
        $response = mysqli_query($this->conn, $query);
        if($response){
            if($response->num_rows > 0){
                $row = mysqli_fetch_assoc($response);
                $this->setUser($row['first_name'], $row['middle_name'], $row['last_name'], $row['gender'], $row['dob'], $row['email'], $row['password'], $row['contact'], $row['province'], $row['district'], $row['isVdc'], $row['area_name'], $row['ward'], $row['role'], $row['profile_pic'], $row['citizenship_number'], $row['citizenship_front_pic'], $row['citizenship_back_pic'], $row['account_state'], $row['register_date']);
            }else{
                $this->setUser("-", "-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-");
            }
        }
    }

    public function updatePassword($newPassword)
    {       
        $query = "update `user` set password='$newPassword' where user_id = '$this->userId'";
        $response = mysqli_query($this->conn, $query);
        return ($response) ? true : false;
    }

    public function updateUser()
    {
        $query = "";
        if ($this->middleName == '')
            $query = "update `user` set first_name = '$this->firstName', middle_name = '',last_name = '$this->lastName', gender = '$this->gender', dob = '$this->dob', contact = '$this->contact', province = '$this->province', district = '$this->district', isVdc = '$this->isVdc', area_name = '$this->areaName', ward = '$this->wardNumber' where user_id = '$this->userId'";
        else
            $query = "update `user` set first_name = '$this->firstName', middle_name = '$this->middleName', last_name = '$this->lastName', gender = '$this->gender', dob = '$this->dob', contact = '$this->contact', province = '$this->province', district = '$this->district', isVdc = '$this->isVdc', area_name = '$this->areaName', ward = '$this->wardNumber' where user_id = '$this->userId'";

        $response = mysqli_query($this->conn, $query);
        return ($response) ? true : false;
    }

    public function updateUserPhoto($newUserPhoto, $userId){
        $query = "update `user` set profile_pic = '$newUserPhoto' where user_id = '$userId'";
        $result = $this->conn->query($query);
        return $result ? true: false;
    }

    // valid house check: url tampering test
    public function isValidUser($userId){
        $query = "select * from `user` where user_id = '$userId'";

        $response = mysqli_query($this->conn, $query);
        
        $count = mysqli_num_rows($response);

        return ($count != 0)? true: false;
    }

    public function userOperation($task, $userId){
        $query = "";
        if($task == 'verify')
            $query = "update `user` set account_state = 1 where user_id = $userId";
        elseif($task == 'suspend')
            $query = "update `user` set account_state = 2 where user_id = $userId";
        elseif($task == 'deactivate')
            $query = "update `user` set account_state = 3 where user_id = $userId";
        
        $result = $this->conn->query($query);

        return $result?true:false;
    }
}