<?php

require_once("../SimpleRest.php");
require_once("User.php");
require_once '../settings/Settings.php';

class UserRestHandler extends SimpleRest {

    function __construct() {
        parent::__construct();
        $this->authorized = $this->authorize();
    }

    public function getAllUsers() {
        if ($this->authorized) {
            $user = new User();
            $rawData = $user->getAllUsers();

            if (empty($rawData)) {
                $statusCode = 404;
                $rawData = array('error' => 'No users found!');
            } else {
                $statusCode = 200;
            }

            $requestContentType = 'application/json';
            $this->setHttpHeaders($requestContentType, $statusCode);

            $result["users"] = $rawData;

            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }
	public function getOldestUsers($tree_id) {
        if ($this->authorized) {
            $user = new User();
            $settings = new Settings(NULL,"tree_width");
            $ress = $settings->getSettingsByKey("tree_width");
            $oldest = $user->getOldestUsers($tree_id, $ress['value']);
			//$root = $user->getRootNotFull($tree_id, $ress['value']);
			$rr = array();
			/*foreach($root as $roo){
				$rr[] = $roo;
			}*/
			foreach($oldest as $old){
				$rr[] = $old;
			}
            if (empty($rr)) {
                $statusCode = 404;
                $oldest = array('error' => 'No users found!');
            } else {
                $statusCode = 200;
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);

            $result["results"] = $rr;

            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }

	public function updateBalance($userData) {
        if ($this->authorized) {
            $user = new User($userData->id);
            $user->gender = NULL;
            $user->balance = $userData->balance;
            $res = $user->updateBalance($userData->tree_id);
            if (empty($res)) {
                $statusCode = 404;
                //$res = array('error' => 'something went wrong!');
				$res=array($user);
            } elseif ($res) {
                $statusCode = 200;
                $res = array('success' => 'user balance updated successfully..');
            } else {
                $statusCode = 200;
                $res = array('faild' => 'failded to updated user balance please check your parametes to be correct..');
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);
            $result["result"] = $res;
            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }
    public function checkUser($userData) {
        if ($this->authorized) {
            $user = new User(NULL, $userData->username);
            $rawData = $user->checkUserExist();

            if (empty($rawData)) {
                $statusCode = 404;
                $rawData = array('error' => 'No users found!');
            } else {
                $statusCode = 200;
            }

            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);

            $result["user"] = $rawData;

            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }

    public function getUser($id) {
        if ($this->authorized) {
            $user = new User($id);
            $res = $user->getUser();
            if (empty($res)) {
                $statusCode = 404;
                $res = array('error' => 'something went wrong!');
            } else {
                $statusCode = 200;
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);

            $result["result"] = $res;

            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }

    public function addUser() {
        if ($this->authorized) {
            //$uploadedPath = $this->uploadImage();
            $uploadedPath=true;
            if ($uploadedPath) {
                $user = new User(NULL, $_POST['username'], $_POST['password'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['mobile'], 1, $_POST['birthday']);
                $res = $user->addUser();
                if (empty($res)) {
                    $statusCode = 404;
                    $res = array('error' => 'something went wrong!');
                } elseif ($res != 0) {
                    $u = $user->checkUserExist();
                    $to = $u['email'];
                    $subject = "Confirm your identity";
                    $message = "<html><head><title>Confirmation email</title></head><body><p>Welcome" . $u['first_name'] . " " . $u['last_name'] . " to MASAM APP.. <br/> your code is " . $u['guid'] . "  please use it to confirm your identity on the APP.</p></body></html>";
                    // Always set content-type when sending HTML email
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                    // More headers
                    $headers .= 'From: <masam@super-tech.net>' . "\r\n";
                    $headers .= 'Cc: ekarhilli@gmail.com' . "\r\n";
                    if (!mail($to, $subject, $message, $headers)) {
                        $statusCode = 200;
                    } else {
                        $statusCode = 303;
                    }
                } else {
                    $statusCode = 200;
                    $res = array('faild' => 'failded to add user please check your parametes to be correct..');
                }
            } else {
                $statusCode = 200;
                $res = array('faild' => 'not uploaded correctly please provide an image to upload');
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);

            $result["result"] = $res;

            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }
    public function resendConfirmationMail($id) {
        if ($this->authorized) {
            $user = new User($id);
            $u = $user->getUser();
            $to = $u['email'];
            $subject = "Confirm your identity";

            $message = "<html><head><title>Confirmation email</title></head><body><p>Welcome" . $u['first_name'] . " " . $u['last_name'] . " to MASAM APP.. <br/> your code is " . $u['guid'] . "  please use it to confirm your identity on the APP.</p></body></html>";

            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            $headers .= 'From: <masam@super-tech.net>' . "\r\n";
            $headers .= 'Cc: ekarhilli@gmail.com' . "\r\n";

            if (!mail($to, $subject, $message, $headers)) {
                $statusCode = 200;
            } else {
                $statusCode = 303;
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);
        } else {
            $this->sendErrorAuth();
        }
    }
    public function deactivateUser($id) {
        if ($this->authorized) {
            $user = new User($id);
            $res = $user->deactivate();
            if (empty($res)) {
                $statusCode = 404;
                $res = array('error' => 'something went wrong!');
            } elseif ($res) {
                $statusCode = 200;
                $res = array('success' => 'user deactivated successfully..');
            } else {
                $statusCode = 200;
                $res = array('faild' => 'failded to deactivated user please check your parametes to be correct..');
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);

            $result["result"] = $res;

            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }

    public function updateUser($userData) {
        if ($this->authorized) {
            $user = new User($userData->id);
            $user->gender = NULL;
            /*$res = $user->getUser();
            $user = $this->convertToObject($res);*/
            $userData = json_decode(json_encode($userData), true);
            foreach ($userData as $key => $value) {
                if ($key != "id")
                    $user->$key = $value;
            }
            $res = $user->updateUser();
            if (empty($res)) {
                $statusCode = 404;
                $res = array('error' => 'something went wrong!');
            } elseif ($res) {
                $statusCode = 200;
                $res = array('success' => 'user updated successfully..');
            } else {
                $statusCode = 200;
                $res = array('faild' => 'failded to updated user please check your parametes to be correct..');
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);
            $result["result"] = $res;
            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }

    public function updateUserImage() {
        if ($this->authorized) {
            $uploadedPath = $this->uploadImage();
            if ($uploadedPath) {
                $user = new User($_POST['id']);
                $user->gender = NULL;
                $user->image  = $uploadedPath;
                $res = $user->updateUser();
                if (empty($res)) {
                    $statusCode = 404;
                    $res = array('error' => 'something went wrong!');
                } elseif ($res) {
                    $statusCode = 200;
                    $res = array('success' => 'user updated successfully..');
                } else {
                    $statusCode = 200;
                    $res = array('faild' => 'failded to updated user please check your parametes to be correct..');
                }
            } else {
                $statusCode = 200;
                $res = array('faild' => 'not uploaded correctly please provide an image to upload');
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);
            $result["result"] = $res;
            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }

    public function activate($id) {
        if ($this->authorized) {
            $user = new User($id);
            $res = $user->activate();
            if (empty($res)) {
                $statusCode = 404;
                $res = array('error' => 'something went wrong!');
            } elseif ($res) {
                $statusCode = 200;
                $res = array('success' => 'user activated successfully..');
            } else {
                $statusCode = 200;
                $res = array('faild' => 'failded to activate user please check your parametes to be correct..');
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);
            $result["result"] = $res;
            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }

    public function accept($id) {
        if ($this->authorized) {
            $user = new User($id);
            $res = $user->acceptChanges($id);
            if (empty($res)) {
                $statusCode = 404;
                $res = array('error' => 'something went wrong!');
            } else {
                $statusCode = 200;
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);

            $result["result"] = $res;

            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }
    
    public function changePassword($userData) {
        if ($this->authorized) {
            $user = new User($userData->id, NULL, $userData->password);
            $res = $user->updatePassword();
            if (empty($res)) {
                $statusCode = 404;
                $res = array('error' => 'something went wrong!');
            } elseif ($res) {
                $statusCode = 200;
                $res = array('success' => 'user password updated successfully..');
            } else {
                $statusCode = 200;
                $res = array('faild' => 'failded to updated password please check your parametes to be correct..');
            }
            $requestContentType = 'application/json'; //$_POST['HTTP_ACCEPT'];
            $this->setHttpHeaders($requestContentType, $statusCode);

            $result["result"] = $res;

            if (strpos($requestContentType, 'application/json') !== false) {
                $response = $this->encodeJson($result);
                echo $response;
            }
        } else {
            $this->sendErrorAuth();
        }
    }

    function convertToObject($array) {
        $object = new User($array['id']);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = convertToObject($value);
            }
            if ($key != "id")
                $object->$key = $value;
        }
        return $object;
    }

    public function encodeJson($responseData) {
        $jsonResponse = json_encode($responseData);
        return $jsonResponse;
    }

}

?>