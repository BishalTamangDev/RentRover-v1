<?php
include_once 'connection_class.php';
class Wishlist extends DatabaseConnection
{
    public $wishlistId;
    public $userId;
    public $roomId;

    public function setWish($userId, $roomId)
    {
        $this->userId = $userId;
        $this->roomId = $roomId;
    }

    public function addWish($url)
    {
        $query = "insert into `wishlist` (user_id, room_id) values('$this->userId', '$this->roomId')";
        $response = mysqli_query($this->conn, $query);
        header("location: $url");
    }

    public function removeWish($url)
    {
        $query = "delete from `wishlist` where user_id = '$this->userId' and room_id = '$this->roomId'";
        $response = mysqli_query($this->conn, $query);
        header("location: $url");
    }

    function fetchWishes()
    {
        $query = "select * from `wishlist`";
        $result = $this->conn->query($query);

        $wishes = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $wishes[] = $row;

        return $wishes;
    }

    // return array
    function fetchWishlistRoomId($userId)
    {
        $userId = $this->conn->real_escape_string($userId);

        $query = "select * from `wishlist` where user_id = '$userId'";
        $result = $this->conn->query($query);

        $wishlistId = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $wishlistId[] = $row['room_id'];

        return $wishlistId;
    }

    public function countWishes($userId)
    {
        $userId = $this->conn->real_escape_string($userId);

        $query = "select * from `wishlist` where user_id = '$userId'";
        $response = mysqli_query($this->conn, $query);
        return mysqli_num_rows($response);
    }

    public function isWish($userId, $roomId)
    {
        $userId = $this->conn->real_escape_string($userId);
        $roomId = $this->conn->real_escape_string($roomId);
        
        $query = "select * from `wishlist` where user_id = '$userId' and room_id = '$roomId'";
        $response = mysqli_query($this->conn, $query);
        $count = mysqli_num_rows($response);
        
        return ($count == 1) ? true : false;
    }
}