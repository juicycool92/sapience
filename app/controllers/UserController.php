<?php
namespace App\controllers;

use Interop\Container\ContainerInterface;
use function MongoDB\BSON\toJSON;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController {
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }
    public function login( Request $req, Response $res, Array $args ){
        $reqUserId= $req->getParam('userId');
        $reqUserPw =  $req->getParam('userPw');
        $query = $this->container->db->prepare('SELECT * FROM USER WHERE USER_ID = :userId AND PASSWORD = SHA2(:userPw,512)');
        $query->bindParam(':userId', $reqUserId, \PDO::PARAM_STR);
        $query->bindParam(':userPw', $reqUserPw, \PDO::PARAM_STR);
        try {
            $query->execute();
            $data= $query->fetchAll();
            if ( sizeof($data) != 1 ) {
                throw new \PDOException('duped id or no result found');
            }
            $_SESSION["user_id"] = ($data[0])["user_id"];

            return $res->withRedirect($this->container->router->pathFor('board'));
        } catch( \PDOException $e ) {
            error_log($e);
            return $this->container->view->render($res, 'home.twig',['msg'=>'wrong ID or PW']);
        }

    }
    public function logOut( Request $req, Response $res, Array $args ){
        session_destroy();
        return $res->withRedirect("/home");
    }

    public function modify( Request $req, Response $res, Array $args ){
        $reqUserId= $args['user_id'];
        $query = $this->container->db->prepare('SELECT * FROM USER WHERE USER_ID = :userId');
        $query->bindParam(':userId', $reqUserId, \PDO::PARAM_STR);
        try {
            $query->execute();
            $data= $query->fetchAll();
            if ( sizeof($data) != 1 ) {
                throw new \PDOException('duped id or no result found');
            }
            return $this->container->view->render($res, 'user/modify.twig',$data);
        } catch( \PDOException $e ) {
            error_log($e);
            return $this->container->view->render($res, 'home.twig',['msg'=>'wrong ID or PW']);
        }
    }
}