<?php
require_once 'config/database.php';
require_once 'controllers/UserController.php';

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);

$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($action) {
    case 'create':
        $userController->create();
        break;
    case 'edit':
        $userController->edit($id);
        break;
    case 'delete':
        $userController->delete($id);
        break;
    case 'getAvailableAccounts':
        $userController->getAvailableAccounts();
        break;
    case 'addAccounts':
        $userController->addAccounts();
        break;
    case 'deleteAccount':
        $userController->deleteAccount($_GET['id']);
        break;
    case 'editAccount':
        $userController->editAccount($_GET['id']);
        break;
    case 'getAccountUsers':
        $userController->getAccountUsers();
        break;
    case 'getExpiringUsers':
        $userController->getExpiringUsers();
        break;
    case 'ajaxEditAccount':
        $userController->ajaxEditAccount();
        break;
    case 'ajaxDeleteAccount':
        $userController->ajaxDeleteAccount($id);
        break;
    default:
        $userController->index();
        break;
}