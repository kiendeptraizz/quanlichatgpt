<?php
class Account
{
    private $conn;
    private $table_name = "accounts";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAvailableAccounts($month_year)
    {
        $query = "SELECT a.*, 
                  (SELECT COUNT(*) FROM users u 
                   WHERE u.account = a.account_name 
                   AND DATE_FORMAT(u.start_date, '%Y-%m-01') = a.month_year) as user_count 
                  FROM " . $this->table_name . " a 
                  WHERE a.month_year = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $month_year);
        $stmt->execute();
        return $stmt;
    }

    public function addMonthlyAccounts($month_year, $accounts, $start_date, $end_date)
    {
        try {
            $this->conn->beginTransaction();

            // Kiểm tra tài khoản đã tồn tại
            $checkQuery = "SELECT account_name FROM " . $this->table_name . " 
                          WHERE account_name = :account_name AND month_year = :month_year";
            $checkStmt = $this->conn->prepare($checkQuery);

            $insertQuery = "INSERT INTO " . $this->table_name . " 
                           (account_name, month_year, status, start_date, end_date) 
                           VALUES (:account_name, :month_year, 'available', :start_date, :end_date)";
            $insertStmt = $this->conn->prepare($insertQuery);

            $inserted = false;
            foreach ($accounts as $account) {
                if (!empty(trim($account))) {
                    // Kiểm tra trùng lặp
                    $checkStmt->bindParam(':account_name', trim($account));
                    $checkStmt->bindParam(':month_year', $month_year);
                    $checkStmt->execute();

                    if ($checkStmt->rowCount() == 0) {
                        $insertStmt->bindParam(':account_name', trim($account));
                        $insertStmt->bindParam(':month_year', $month_year);
                        $insertStmt->bindParam(':start_date', $start_date);
                        $insertStmt->bindParam(':end_date', $end_date);
                        $insertStmt->execute();
                        $inserted = true;
                    }
                }
            }

            if ($inserted) {
                $this->conn->commit();
                return true;
            } else {
                throw new Exception("Không có tài khoản mới nào được thêm");
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Database Error: " . $e->getMessage());
            throw new Exception("Lỗi khi thêm tài khoản: " . $e->getMessage());
        }
    }

    public function updateAccountStatus($account_name, $month_year, $status)
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET status = :status 
                 WHERE account_name = :account_name 
                 AND month_year = :month_year";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":account_name", $account_name);
        $stmt->bindParam(":month_year", $month_year);

        return $stmt->execute();
    }

    public function deleteAccount($id)
    {
        try {
            $this->conn->beginTransaction();

            // Lấy thông tin tài khoản trước khi xóa
            $account = $this->getAccountById($id);
            if (!$account) {
                throw new Exception("Không tìm thấy tài khoản");
            }

            // Kiểm tra xem có user nào đang sử dụng không
            $users = $this->getUsersByAccount($account['account_name'], $account['month_year']);
            if (count($users) > 0) {
                throw new Exception("Không thể xóa tài khoản đang được sử dụng bởi " . count($users) . " người dùng");
            }

            // Thực hiện xóa
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$id]);

            $this->conn->commit();
            return $result;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function updateAccount($id, $account_name, $start_date, $end_date)
    {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET account_name = :account_name,
                         start_date = :start_date,
                         end_date = :end_date
                     WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':id' => $id,
                ':account_name' => $account_name,
                ':start_date' => $start_date,
                ':end_date' => $end_date
            ]);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            throw new Exception("Lỗi khi cập nhật tài khoản");
        }
    }

    public function getUserCount($account_name, $month_year)
    {
        $query = "SELECT COUNT(*) FROM users 
                  WHERE account = ? 
                  AND DATE_FORMAT(start_date, '%Y-%m-01') = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$account_name, $month_year]);
        return $stmt->fetchColumn();
    }

    public function getAccountById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsersByAccount($account_name, $month_year)
    {
        $query = "SELECT * FROM users 
                  WHERE account = :account_name 
                  AND DATE_FORMAT(start_date, '%Y-%m-01') = :month_year";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_name', $account_name);
        $stmt->bindParam(':month_year', $month_year);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}