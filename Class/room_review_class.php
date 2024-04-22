<?php
include_once 'connection_class.php';

class RoomReview extends DatabaseConnection
{
    public  $roomReviewId;
    public $roomId;
    public $tenantId;
    public $reviewData;
    public $rating;
    public $state;
    public $reviewDate;


    // overall calculated data
    public $cumulativeRating;
    public $numberOfReviews;

    public function setRoomReview($roomId, $tenantId, $reviewData, $rating, $reviewDate, $state)
    {
        $this->roomId = $roomId;
        $this->tenantId = $tenantId;
        $this->reviewData = $reviewData;
        $this->rating = $rating;
        $this->state = $state;
        $this->reviewDate = $reviewDate;
        $this->reviewData = $this->conn->real_escape_string($reviewData);
    }

    public function registerRoomReview()
    {
        $alreadyReviewed = $this->checkIfAlreadyReviewed($this->tenantId, $this->roomId);
        $query = "insert into `room_review` (room_id, tenant_id, review_data, rating, review_date, state) values('$this->roomId', '$this->tenantId', '$this->reviewData', '$this->rating', '$this->reviewDate', '$this->state')";
        $result = mysqli_query($this->conn, $query);
        return ($result) ? $this->getImmediateRoomReviewId() : false;
    }

    public function checkIfAlreadyReviewed($tenantId, $roomId)
    {
        $query = "select * from `room_review` where room_id = '$roomId' and tenant_id = '$tenantId'";
        $result = $this->conn->query($query);
        return ($result->num_rows == 0) ? false : true;
    }

    public function getRoomRatings($roomId)
    {
        $query = "select rating from `room_review` where room_id = '$roomId'";
        $result = $this->conn->query($query);

        $ratingArray = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc())
                $ratingArray[] = $row['rating'];
        }

        return $ratingArray;
    }

    public function setFinalRating($roomId)
    {
        $query = "select rating from `room_review` where room_id = '$roomId'";
        $result = $this->conn->query($query);

        $ratingArray = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc())
                $ratingArray[] = $row['rating'];
        }

        $this->numberOfReviews = count($ratingArray);
        $this->cumulativeRating = 0;

        if (sizeof($ratingArray) != 0)
            $this->cumulativeRating = array_sum($ratingArray) / count($ratingArray);
    }

    private function getImmediateRoomReviewId()
    {
        $query = "select * from `room_review` where room_id = '$this->roomId' and tenant_id = '$this->tenantId' and  review_date = '$this->reviewDate'";
        $result = $this->conn->query($query);
         $roomReviewIdArray = [];

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
             $roomReviewIdArray[] = $row;
        }

        foreach ( $roomReviewIdArray as $temp)
             $roomReviewId = $temp['room_review_id'];

        return  $roomReviewId;
    }

    function fetchAllRoomReview($roomId)
    {
        $query = "select * from `room_review` where room_id = '$roomId'";
        $roomReviews = [];
        $result = $this->conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $roomReviews[] = $row;
            }
        }

        return $roomReviews;
    }

    public function fetchRoomReview($reviewId)
    {
        $query = "select * from `room_review` where room_review_id = '$reviewId'";
        $response = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($response);

        $this->setRoomReview($row['room_id'], $row['tenant_id'], $row['review_data'], $row['rating'], $row['review_date'], $row['state']);
    }

    public function fetchMyRoomReview($tenantId, $roomId)
    {
        $reviewSets = [];
        $query = "select * from `room_review` where tenant_id = '$tenantId' and room_id = '$roomId'";
        $result = $this->conn->query($query);
        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $reviewSets[] = $row;
                }
            }
            // $this->setRoomReview($row['room_id'], $row['tenant_id'], $row['review_data'], $row['rating'], $row['review_date'], $row['state']);
            // $this->id = $row['id'];
            // return true;
        } else {
            // return false;
        }
        return $reviewSets;
    }
}