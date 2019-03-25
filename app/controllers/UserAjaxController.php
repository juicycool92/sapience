<?php
namespace App\controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UserAjaxController {
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function signIn( Request $req, Response $res, Array $args ){
        $reqUserId= $req->getParam('userId');
        $reqUserName =  $req->getParam('userName');
        $reqUserPw =  $req->getParam('userPassword');
        $query = $this->container->db->prepare('INSERT INTO USER (USER_ID, USER_NAME, PASSWORD) VALUES ( :userId, :userName, SHA2( :userPassword, 512 ))');
        $query->bindParam(':userId', $reqUserId, \PDO::PARAM_STR);
        $query->bindParam(':userName', $reqUserName, \PDO::PARAM_STR);
        $query->bindParam(':userPassword', $reqUserPw, \PDO::PARAM_STR);
        try {
            $query->execute();
        } catch (\PDOException  $e) {
            $msg = 'unknownError';
            switch ( $e->getCode() ) {
                case '23000' : $msg = 'alreadyExistUserId';break;
                default:break;
            }
            return $res->withJson(array('statusCode'=>'0','msg'=>$msg),400);
        }
        return $res->withJson(array('statusCode'=>'1'),200);
    }


    public function modify( Request $req, Response $res, Array $args ) {
        $userId = $this->getSessionUserId();
        if ( $userId === null )
            return $res->withStatus(401);
        $reqUserName = $req->getParam("userName");
        $reqUserPw = $req->getParam("userPw");
        $query = $this->container->db->prepare('UPDATE USER SET USER_NAME = :userName, PASSWORD = SHA2( :userPw, 512) WHERE USER_ID = :userId');
        $query->bindParam(':userName', $reqUserName, \PDO::PARAM_STR);
        $query->bindParam(':userPw', $reqUserPw, \PDO::PARAM_STR);
        $query->bindParam(':userId', $userId, \PDO::PARAM_STR);
        try {
            $query->execute();
            return $res->withStatus(200);
        } catch ( \PDOException $e ) {
            error_log($e);
            return $res->withStatus(400);
        }
    }
    protected function getSessionUserId () {
        if ( isset($_SESSION['user_id']) ) {
            return $_SESSION['user_id'];
        }else {
            return null;
        }
    }
}