<?php

require_once("UserRestHandler.php");

$view = "";
if (isset($_GET["view"]))
    $view = $_GET["view"];
/*
  controls the RESTful services
  URL mapping
 */
$userRestHandler = new UserRestHandler();

switch ($view) {
    case "all":
        // to handle REST Url /User/list/
        $userRestHandler->getAllUsers();
        break;
    case "add":
        // to handle REST Url /User/add/
        // get posted data
        $data = json_decode(file_get_contents("php://input"));
        $userRestHandler->addUser($data);
        break;
    case "edit":
        // to handle REST Url /User/edit/
        // get posted data
        $data = json_decode(file_get_contents("php://input"));
        $userRestHandler->updateUser($data);
        break;
    case "update_image":
        // to handle REST Url /User/upload/
        // get posted data
        $data = json_decode(file_get_contents("php://input"));
        $userRestHandler->updateUserImage();
        break;
	case "update_balance":
        // to handle REST Url /User/update/
        // get posted data
        $data = json_decode(file_get_contents("php://input"));
        $userRestHandler->updateBalance($data);
        break;
    case "get":
        // to handle REST Url /User/get/
        // get posted data
        $data = $_GET['id'];
        $userRestHandler->getUser($data);
        break;
    case "reset_password":
        // to handle REST Url /User/reset/
        // get posted data
        $data = json_decode(file_get_contents("php://input"));
        $userRestHandler->changePassword($data);
        break;
    case "check_user_exist":
        // to handle REST Url /User/list/
        // get posted data
        $data = json_decode(file_get_contents("php://input"));
        $userRestHandler->checkUser($data);
        break;
    case "deactivate":
        // to handle REST Url /User/list/
        // get posted data
        $data = $_GET['id'];
        $userRestHandler->deactivateUser($data);
        break;
        case "activate":
        // to handle REST Url /User/list/
        // get posted data
        $data = $_GET['id'];
        $userRestHandler->activate($data);
        break;
	case "oldest_notfull":
        // to handle REST Url /User/list/
        // get posted data
        $data = $_GET['tree_id'];
        $userRestHandler->getOldestUsers($data);
        break;
    case "accept":
        // to handle REST Url /User/list/
        // get posted data
        $data = $_GET['id'];
        $userRestHandler->accept($data);
        break;
    case "mail_resend":
        // to handle REST Url /User/list/
        // get posted data
        $data = $_GET['id'];
        $userRestHandler->resendConfirmationMail($data);
        break;
    case "" :
        //404 - not found;
        break;
}
?>