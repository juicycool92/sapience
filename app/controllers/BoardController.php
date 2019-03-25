<?php
namespace App\controllers;

use Interop\Container\ContainerInterface;
use function MongoDB\BSON\toJSON;
use Slim\Http\Request;
use Slim\Http\Response;

class BoardController {
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function index($req, $res){
        $userId = '';
        if ( isset($_SESSION['user_id']) ) {
            $userId = $_SESSION['user_id'];
        }
        return $this->container->view->render($res, 'board/list.twig',['userId'=>$userId]);
    }

    public function detail( Request $req, Response $res, Array $args ) {
        $reqBoardNum = $args['board_no'];
        $data = $this->container->db->prepare('SELECT B.*, U.user_name FROM BOARD as B INNER JOIN user as U on U.user_id = B.user_id WHERE B.NO = :boardNo');
        $data->bindParam(':boardNo', $reqBoardNum, \PDO::PARAM_INT);
        $data->execute();
        $jsonData = $data->fetchAll(\PDO::FETCH_OBJ);
        return $this->container->view->render($res,'board/detail.twig',
            ['board'=>$jsonData[0]]);

    }

    public function write ( Request $req, Response $res, Array $args ) {
        return $this->container->view->render($res, 'board/write.twig');
    }
    public function modify ( Request $req, Response $res, Array $args ) {
        $reqBoardNum = $args['boardNo'];
        $query = $this->container->db->prepare('SELECT * FROM BOARD WHERE NO = :boardNo');
        $query->bindParam(':boardNo',$reqBoardNum, \PDO::PARAM_INT);
        $returnArr = null;
        try {
            $query->execute();
            $data = $query->fetchAll();
            if ( sizeof($data) != 1 ) {
                throw new \PDOException('select board error');
            }
            if ( $_SESSION['user_id'] !== $data[0]['userId']) {
                throw new \PDOException('unauthorized request');
            }
            $returnArr = $data[0];
        } catch( \PDOException $e ) {
            error_log($e);
            return $res->withRedirect($this->container->router->pathFor('board'));
        }
        return $this->container->view->render($res, 'board/modify.twig',$returnArr);
    }
}