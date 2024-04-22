<?php
include_once 'connection_class.php';
class Admin extends DatabaseConnection
{
    public $adminId;
    public $firstName;
    public $middleName;
    public $lastName;
    public $email;
    public $password;
    public $contact;
    public $registerDate;

    public function setAdmin($firstName, $middleName, $lastName, $email, $password, $contact, $registerDate)
    {
        $this->firstName = $this->conn->real_escape_string($firstName);
        $this->middleName = $this->conn->real_escape_string($middleName);
        $this->lastName = $this->conn->real_escape_string($lastName);
        $this->email = $this->conn->real_escape_string($email);
        $this->password = $this->conn->real_escape_string($password);
        $this->contact = $this->conn->real_escape_string($contact);
        $this->registerDate = $registerDate;
    }

    public function register()
    {
        $query = "insert into `admin` (first_name, middle_name, last_name, email, password, contact, register_date) 
                                values('$this->firstName', '$this->middleName', '$this->lastName', '$this->email', '$this->password', '$this->contact', '$this->registerDate')";

        $response = mysqli_query($this->conn, $query);

        if ($response)
            header("location: login.php");
    }

    public function validateEmail($email)
    {
        $email = $this->conn->real_escape_string($email);
        $query = "select * from `admin` where email = '$email'";
        $response = mysqli_query($this->conn, $query);
        if (mysqli_num_rows($response) > 0) {
            $this->email = $email;
            return true;
        }
    }

    // returning the id of a admin 
    public function getAdminId($email)
    {
        $email = $this->conn->real_escape_string($email);
        $query = "select admin_id from `admin` where email = '$email'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);
        return $row['admin_id'];
    }

    public function fetchAdmin($adminId)
    {
        $query = "select * from `admin` where admin_id = '$adminId'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);
        $this->setAdmin($row['first_name'], $row['middle_name'], $row['last_name'], $row['email'], $row['password'], $row['contact'], $row['register_date']);
    }

    public function updateAdmin($adminId)
    {
        $query = "update `admin` set firstName='$this->firstName', middleName='$this->middleName', lastName='$this->lastName', email='$this->email', password='$this->password', contact='$this->contact'";
        $response = mysqli_query($this->conn, $query);
        return $response ? true : false;
    }
}