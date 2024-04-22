<?php
include_once 'connection_class.php';
class Feedback extends DatabaseConnection
{
    public $feedbackId;
    public $userId;
    public $feedbackData;
    public $rating;
    public $responseData;
    public $isResponded;
    public $feedbackDate;
    public $responseDate;

    public function setFeedback($userId, $feedbackData, $rating)
    {
        $this->userId = $userId;
        $this->feedbackData = $this->conn->real_escape_string($feedbackData);
        $this->rating = $rating;
        $this->feedbackDate = date('Y-m-d H:i:s');
    }

    public function setResponse($feedbackId, $responseData)
    {
        $this->feedbackId = $this->conn->real_escape_string($feedbackId);
        $this->responseData = $this->conn->real_escape_string($responseData);
        $this->isResponded = true;
        $this->responseDate = date('Y-m-d H:i:s');
    }

    public function registerFeedback()
    {
        $query = "insert into `feedback` (user_id, feedback_data, rating, feedback_date) values ('$this->userId', '$this->feedbackData', '$this->rating', '$this->feedbackDate')";
        $response = mysqli_query($this->conn, $query);
        return $response ? $this->getImmediateFeedbackId() : false;
    }

    public function registerResponse()
    {
        $query = "insert into `feedback` (response_data, is_responded, feedback_date) values('$this->responseData', 'true', '$this->responseDate')";
        $response = mysqli_query($this->conn, $query);
        return $response ? $this->getImmediateFeedbackId() : false;
    }

    private function getImmediateFeedbackId()
    {
        $query = "select feedback_id from `feedback` where user_id = '$this->userId' and feedback_data = '$this->feedbackData' and feedback_date = '$this->feedbackDate'";
        $result = $this->conn->query($query);
        $feedbackIdArray = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $feedbackIdArray[] = $row;

        foreach ($feedbackIdArray as $temp)
            $feedbackId = $temp['feedback_id'];

        return $feedbackId;
    }

    public function countFeedback($feedbackState)
    {
        if ($feedbackState == 'all')
            $query = 'select feedback_id from `feedback`';
        else if ($feedbackState == "unreplied")
            $query = 'select feedback_id from `feedback` where is_responded = 0';
        else
            $query = 'select feedback_id from `feedback` where is_responded = 1';

        $response = mysqli_query($this->conn, $query);
        return mysqli_num_rows($response);
    }

    public function fetchFeedback($which)
    {
        // $which = "all" || "latest";
        if ($which == "latest") {
            $query = "select * from `feedback` order by feedback_id desc limit 1";
            $result = $this->conn->query($query);
            $feedbacks = [];

            if ($result->num_rows > 0)
                while ($row = $result->fetch_assoc())
                    $feedbacks[] = $row;

            return $feedbacks;
        } elseif ($which == "all") {
            $query = "select * from `feedback` order by feedback_id desc";
            $result = $this->conn->query($query);
            $feedbacks = [];

            if ($result->num_rows > 0)
                while ($row = $result->fetch_assoc())
                    $feedbacks[] = $row;

            return $feedbacks;
        }
    }

    public function fetchAllFeedbacks()
    {
        $query = "select * from `feedback` order by feedback_id desc";
        $result = $this->conn->query($query);
        $feedbacks = [];

        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $feedbacks[] = $row;

        return $feedbacks;
    }

    // feedback set for displaying : feature display 
    public function fetchFeedbackSet()
    {
        $query = "select * from `feedback` order by feedback_id desc";
        $result = $this->conn->query($query);

        $feedbacks = [];
        if ($result->num_rows <= 3) {
            while ($row = $result->fetch_assoc())
                $feedbacks[] = $row;

            return $feedbacks;
        } else {
            // max rating feedback
            $query = "select * from `feedback` order by rating desc";
            $result = $this->conn->query($query);
            $feedbacks[] = $result->fetch_assoc();

            // min rating feedback
            $query = "select * from `feedback` order by rating asc";
            $result = $this->conn->query($query);
            $feedbacks[] = $result->fetch_assoc();

            // average rating feeedback
            $maxRating = $feedbacks[0]['rating'];
            $minRating = $feedbacks[1]['rating'];
            $averageRating = ($maxRating + $minRating) / 2;

            // $query = "select * from `feedback` where rating > '$minRating' and rating < '$maxRating' order by rating desc";
            $query = "select * from `feedback` where rating <= '$averageRating' order by rating desc";
            $result = $this->conn->query($query);

            if ($result->num_rows > 0)
                $feedbacks[] = $result->fetch_assoc();

            return $feedbacks;
        }
    }
}