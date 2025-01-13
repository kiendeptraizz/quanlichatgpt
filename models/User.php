<?php
class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $subscription_plan;
    public $account;
    public $start_date;
    public $end_date;
    public $status;
    public $email;
    public $facebook_link;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        try {
            $query = "INSERT INTO " . $this->table_name . "
                    (username, subscription_plan, account, start_date, end_date, status, email, facebook_link)
                    VALUES (:username, :subscription_plan, :account, :start_date, :end_date, :status, :email, :facebook_link)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":subscription_plan", $this->subscription_plan);
            $stmt->bindParam(":account", $this->account);
            $stmt->bindParam(":start_date", $this->start_date);
            $stmt->bindParam(":end_date", $this->end_date);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":facebook_link", $this->facebook_link);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            throw new Exception("Lỗi khi tạo người dùng");
        }
    }

    public function getOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                SET username = :username,
                    subscription_plan = :subscription_plan,
                    account = :account,
                    start_date = :start_date,
                    end_date = :end_date,
                    status = :status,
                    email = :email,
                    facebook_link = :facebook_link
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":subscription_plan", $this->subscription_plan);
        $stmt->bindParam(":account", $this->account);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":facebook_link", $this->facebook_link);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    public function getExpiringUsers($days = 7)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE end_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
