<?php
require_once 'models/User.php';
require_once 'models/Account.php';

class UserController
{
    private $user;
    private $account;

    public function __construct($db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->user = new User($db);
        $this->account = new Account($db);
    }

    public function index()
    {
        $users = $this->user->getAll();
        $account = $this->account;
        require_once 'views/users/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->user->username = $_POST['username'];
                $this->user->subscription_plan = $_POST['subscription_plan'] . " tháng";
                $this->user->account = $_POST['account'];
                $this->user->start_date = $_POST['start_date'];
                $this->user->end_date = $_POST['end_date'];
                $this->user->status = $_POST['status'];
                $this->user->email = $_POST['email'];
                $this->user->facebook_link = $_POST['facebook_link'] ?? null;

                if ($this->user->create()) {
                    $_SESSION['success'] = "Thêm người dùng thành công";
                    header("Location: index.php");
                    exit();
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }

        $availableAccounts = $this->account->getAvailableAccounts();
        require_once 'views/users/create.php';
    }

    public function addAccounts()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (empty($_POST['start_date']) || empty($_POST['end_date']) || empty($_POST['accounts'])) {
                    throw new Exception("Vui lòng nhập đầy đủ thông tin");
                }

                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];

                if (strtotime($end_date) <= strtotime($start_date)) {
                    throw new Exception("Ngày kết thúc phải sau ngày bắt đầu");
                }

                // Tách và lọc danh sách tài khoản
                $accounts = array_filter(
                    array_map('trim', explode("\n", $_POST['accounts'])),
                    function ($account) {
                        return !empty($account);
                    }
                );

                if (empty($accounts)) {
                    throw new Exception("Danh sách tài khoản không hợp lệ");
                }

                if ($this->account->addMonthlyAccounts($accounts, $start_date, $end_date)) {
                    $_SESSION['success'] = "Thêm tài khoản thành công";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header("Location: index.php");
            exit();
        }
    }

    public function edit($id)
    {
        try {
            $this->user->id = $id;
            $userData = $this->user->getOne();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->user->username = $_POST['username'];
                $this->user->subscription_plan = $_POST['subscription_plan'] . " tháng";
                $this->user->account = $_POST['account'];
                $this->user->start_date = $_POST['start_date'];
                $this->user->end_date = $_POST['end_date'];
                $this->user->status = $_POST['status'];
                $this->user->email = $_POST['email'];
                $this->user->facebook_link = $_POST['facebook_link'] ?? null;

                if ($this->user->update()) {
                    $_SESSION['success'] = "Cập nhật người dùng thành công";
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Cập nhật người dùng thất bại";
                }
            }

            require_once 'views/users/edit.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php");
            exit();
        }
    }

    public function delete($id)
    {
        try {
            $this->user->id = $id;
            if ($this->user->delete()) {
                $_SESSION['success'] = "Xóa người dùng thành công";
            } else {
                $_SESSION['error'] = "Xóa người dùng thất bại";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header("Location: index.php");
        exit();
    }

    public function getAvailableAccounts()
    {
        try {
            $stmt = $this->account->getAvailableAccounts();
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Chuyển đổi dữ liệu ngày tháng sang định dạng phù hợp
            foreach ($accounts as &$account) {
                $account['start_date'] = date('Y-m-d', strtotime($account['start_date']));
                $account['end_date'] = date('Y-m-d', strtotime($account['end_date']));
            }

            header('Content-Type: application/json');
            echo json_encode($accounts);
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteAccount($id)
    {
        try {
            if ($this->account->deleteAccount($id)) {
                $_SESSION['success'] = "Xóa tài khoản thành công";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header("Location: index.php");
        exit();
    }

    public function editAccount($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $account_name = $_POST['account_name'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];

                if ($this->account->updateAccount($id, $account_name, $start_date, $end_date)) {
                    $_SESSION['success'] = "Cập nhật tài khoản thành công";
                    header("Location: index.php");
                    exit();
                }
            }

            $accountData = $this->account->getAccountById($id);
            require_once 'views/accounts/edit.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php");
            exit();
        }
    }

    public function getAccountUsers()
    {
        if (isset($_GET['account_name']) && isset($_GET['month_year'])) {
            try {
                $users = $this->account->getUsersByAccount(
                    $_GET['account_name'],
                    $_GET['month_year']
                );
                header('Content-Type: application/json');
                echo json_encode($users);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function getExpiringUsers()
    {
        $users = $this->user->getExpiringUsers();

        // Debug log để kiểm tra số lượng người dùng được lấy ra
        $userCount = $users->rowCount();
        error_log('Number of expiring users fetched: ' . $userCount);

        require_once 'views/users/expiring.php';
    }

    public function ajaxEditAccount()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $account_name = $_POST['account_name'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            // Cập nhật tài khoản
            $this->account->updateAccount($id, $account_name, $start_date, $end_date);
            echo json_encode(['success' => 'Cập nhật tài khoản thành công']);
        } else {
            echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
        }
    }

    public function ajaxDeleteAccount($id)
    {
        try {
            if ($this->account->deleteAccount($id)) {
                echo json_encode(['success' => 'Xóa tài khoản thành công']);
            } else {
                echo json_encode(['error' => 'Không thể xóa tài khoản']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}