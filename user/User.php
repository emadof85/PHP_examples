<?php

require_once("../dbcontroller.php");
/*
  A domain Class to demonstrate RESTful web services
 */

Class User {

    private $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;
    public $email;
    public $mobile;
    public $gender;
    public $birthday;
    public $image;
    public $balance;
    public $activated;
    public $guid;
    public $root;

    function __construct($id = NULL, $username = NULL, $pass = NULL, $fname = NULL, $lname = NULL, $mail = NULL, $mob = NULL, $gender = NULL, $birthday = NULL, $image = NULL) {
        $this->id = $id;
        $this->birthday = $birthday;
        $this->email = $mail;
        $this->first_name = $fname;
        $this->gender = $gender;
        $this->last_name = $lname;
        $this->mobile = $mob;
        $this->password = md5($pass);
        $this->username = $username;
        $this->image = $image;
    }

    public function getAllUsers() {
        $query = "SELECT * FROM user";
        $dbcontroller = new DBController();
        return $dbcontroller->executeSelectQuery($query);
    }

    public function getLatestUsers() {
        $query = "SELECT * FROM user order by id desc limit 8";
        $dbcontroller = new DBController();
        return $dbcontroller->executeSelectQuery($query);
    }

    public function getOldestUsers($tid, $width) {
        $query = "SELECT distinct u.* "
                . "FROM (SELECT distinct parent, COUNT(*) as counter FROM registry inner join user on user.id=registry.parent Where registry.tree_id=$tid GROUP BY registry.parent) as counts inner join "
                . "user as u on u.id=counts.parent where counts.counter < $width order by u.id asc limit 10";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executeSelectQuery($query);
        $query1 = "SELECT distinct u.* from user u inner join"
                . " (SELECT * FROM registry r Where r.tree_id=$tid and r.user_id not in (SELECT parent from registry WHERE parent IS NOT NULL)) as leafs"
                . " on u.id = leafs.user_id";
        $res1 = $dbcontroller->executeSelectQuery($query1);
        if ($res) {
            $r = array_merge($res, $res1);
        } else
            $r = $res1;
        return $r;
    }

    public function getRootNotFull($tid, $width) {
        $query = "SELECT u.* "
                . "FROM (SELECT parent,user_id,COUNT(*) as counter FROM registry, user where registry.parent is null and user.id=registry.user_id and registry.tree_id=$tid GROUP BY registry.parent) as counts inner join "
                . "user as u on u.id=counts.user_id where counts.counter < $width";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executeSelectQuery($query);
        return $res;
    }

    public function addUser() {
        $this->guid = substr(sha1(mt_rand()), 17, 6);
        $query = "INSERT INTO user values(NULL,'$this->username','$this->password',"
                . "'$this->email','$this->mobile',"
                . "'$this->first_name','$this->last_name',$this->gender,"
                . "'$this->birthday','$this->image',0,0,"
                . "'$this->guid',0,0)";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executePLQuery($query);
        if ($res) {
            return $dbcontroller->executeSingleSelectQuery("SELECT LAST_INSERT_ID() as id;")["id"];
        } else
            return 0;
    }

    public function checkUserExist() {
        $query = "SELECT * FROM user Where username='$this->username'";
        $dbcontroller = new DBController();
        return $dbcontroller->executeSingleSelectQuery($query);
    }

    public function getUser() {
        $query = "SELECT * FROM user Where id=$this->id";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executeSingleSelectQuery($query);
        return $res;
    }

    public function getUserByGUID() {
        $query = "SELECT * FROM user Where guid='$this->guid'";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executeSingleSelectQuery($query);
        return $res;
    }

    public function getByUsername() {
        $query = "SELECT * FROM user Where username='$this->username'";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executeSingleSelectQuery($query);
        return $res;
    }

    public function updateBalance($tree_id = 0) {
        if($tree_id == 0){
            $query = "update user set balance=$this->balance where id=$this->id";
        }
        else{
            $query = "update user set balance=$this->balance, tree_id=$tree_id where id=$this->id";
        }
        $dbcontroller = new DBController();
        $res = $dbcontroller->executePLQuery($query);
        if ($res)
            return true;
        else
            return false;
    }

    public function updateUser() {
        $query = "Insert into user_audit(user_id,`key`,`value`,type) values ";
        if (isset($this->mobile))
            $query .= "($this->id,'mobile','$this->mobile','varchar'),";
        if (isset($this->username))
            $query .= "($this->id,'username','$this->username','varchar'),";
        if (isset($this->first_name))
            $query .= "($this->id,'first_name','$this->first_name','varchar'),";
        if (isset($this->last_name))
            $query .= "($this->id,'last_name','$this->last_name','varchar'),";
        if (isset($this->gender))
            $query .= "($this->id,'gender','$this->gender','num'),";
        if (isset($this->birthday))
            $query .= "($this->id,'birthday','$this->birthday','varchar'),";
        if (isset($this->image))
            $query .= "($this->id,'image','$this->image','varchar'),";
        $query = substr_replace($query, "", -1);


        $dbcontroller = new DBController();
        $res = $dbcontroller->executePLQuery($query);
        if ($res)
            return true;
        else
            return false;
    }

    public function acceptChanges($id) {
        $query = "SELECT * FROM user_audit Where id=$id";
        $dbcontroller = new DBController();
        $res_audit = $dbcontroller->executeSingleSelectQuery($query);
        if ($res_audit) {
            $query = "Update user set ";
            $query .= $res_audit['key'] . "=";
            if ($res_audit['type'] == "varchar")
                $query .= "'" . $res_audit['value'] . "'";
            else
                $query .= $res_audit['value'];
            $query .= " WHERE id=" . $res_audit['user_id'];
        }
        $dbcontroller = new DBController();
        $res = $dbcontroller->executePLQuery($query);
        if ($res) {
            $query = "delete from user_audit where id=$id";
            $dbcontroller = new DBController();
            $res = $dbcontroller->executePLQuery($query);
            if ($res) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function updatePassword() {
        $query = "Update user set "
                . "password = '$this->password'"
                . " WHERE id=$this->id";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executePLQuery($query);
        if ($res)
            return true;
        else
            return false;
    }

    public function deactivate() {
        $query = "Update user set activated = 0"
                . " WHERE id=$this->id";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executePLQuery($query);
        if ($res)
            return true;
        else
            return false;
    }

    public function activate() {
        $query = "Update user set activated = 1"
                . " WHERE id=$this->id";
        $dbcontroller = new DBController();
        $res = $dbcontroller->executePLQuery($query);
        if ($res > 0)
            return true;
        else
            return false;
    }

}

?>