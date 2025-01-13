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

                $accounts = array_map('trim', explode(',', $_POST['accounts']));
                $accounts = array_filter($accounts);

                if (empty($accounts)) {
                    throw new Exception("Danh sách tài khoản không hợp lệ");
                }

                if ($this->account->addMonthlyAccounts($accounts, $start_date, $end_date)) {
                    $_SESSION['success'] = "Thêm tài khoản thành công";
                    header("Location: index.php");
                    exit();
                } else {
                    throw new Exception("Không thể thêm tài khoản");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: index.php");
                exit();
            }
        }
    }

    public function edit($id)
    {
        $this->user->id = $id;
        $userData = $this->user->getOne();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->username = $_POST['username'];
            $this->user->subscription_plan = $_POST['subscription_plan'];
            $this->user->account = $_POST['account'];
            $this->user->start_date = $_POST['start_date'];
            $this->user->end_date = $_POST['end_date'];
            $this->user->status = $_POST['status'];
            $this->user->email = $_POST['email'];
            $this->user->facebook_link = $_POST['facebook_link'];

            if ($this->user->update()) {
                header("Location: index.php?action=index");
            }
        }
        require_once 'views/users/edit.php';
    }

    public function delete($id)
    {
        $this->user->id = $id;
        if ($this->user->delete()) {
            header("Location: index.php?action=index");
        }
    }

    public function getAvailableAccounts()
    {
        try {
            $accounts = $this->account->getAvailableAccounts();
            $accountList = $accounts->fetchAll(PDO::FETCH_ASSOC);
            header('Content-Type: application/json');
            echo json_encode($accountList);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
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
