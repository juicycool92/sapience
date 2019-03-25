<?php
namespace App\controllers;
use Slim\Http\Request;
use Slim\Http\Response;
use Interop\Container\ContainerInterface;

class BoardAjaxController {
    protected  $container;
    protected  $chunkSize;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->chunkSize = 3;
    }

    public function selectListByLimit( Request $req, Response $res, Array $args ){
        $idx = $req->getParam("boardIdx");
        $pageInfo = $this->calculateCurrentAndMaxBoardListSize($idx);
        $resultData = $this->selectBoardLimit($idx);
        $resJson = array('pageInfo' => $pageInfo,
                            'resultData'=>$resultData,
                            'pageChunkSize'=>$this->chunkSize);
        return $res->withJson($resJson, 200);
    }
    public function selectListBackByLimit( Request $req, Response $res, Array $args ){
        $idx = $req->getParam("boardIdx");
        $idx -= $this->chunkSize * 2;
        $pageInfo = $this->calculateCurrentAndMaxBoardListSize($idx);
        $resultData = $this->selectBoardLimit($idx);
        $resJson = array('pageInfo' => $pageInfo ,
                            'resultData'=>$resultData ,
                            'pageChunkSize'=>$this->chunkSize);
        return $res->withJson($resJson, 200);
    }

    protected function selectBoardLimit($idx) {
        $query = $this->container->db->prepare('SELECT * FROM BOARD ORDER BY no DESC LIMIT :idx , :sizee;');
        $query->bindParam(':idx', $idx, \PDO::PARAM_INT);
        $query->bindValue(':sizee', $this->chunkSize, \PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchAll(\PDO::FETCH_OBJ);
        return $data;
    }
    protected function countBoardSize() {
        $query = $this->container->db->query('SELECT count(no) as boardSize FROM BOARD');
        $data = $query->fetchAll(\PDO::FETCH_OBJ);
        return $data;
    }
    protected function calculateCurrentAndMaxBoardListSize(int $idx) {
        $currentPage = null;
        $maxPage = null;

        $totalBoardSize = $this->countBoardSize();
        $totalBoardSize = json_decode(json_encode($totalBoardSize[0]),true);
        $maxPage = ceil(($totalBoardSize['boardSize'] / $this->chunkSize ));

        if ( $idx == null )
            $currentPage = 1;
        else
            $currentPage = floor($idx / $this->chunkSize) + 1;
        return array( 'currentPage' => $currentPage, 'maxPage' => $maxPage );
    }
    public function insert( Request $req, Response $res, Array $args ) {
        $userId = $this->getSessionUserId();
        if ( $userId === null )
            return $res->withStatus(401);
        $reqBoardSubject = $req->getParam("boardSubject");
        $reqBoardContext = $req->getParam("boardContext");
        $query = $this->container->db->prepare('INSERT INTO BOARD (USER_ID, SUBJECT, CONTENT) VALUES( :userId, :subject, :context)');
        $query->bindParam(':userId', $userId, \PDO::PARAM_STR);
        $query->bindParam(':subject', $reqBoardSubject, \PDO::PARAM_STR);
        $query->bindParam(':context', $reqBoardContext, \PDO::PARAM_STR);
        try {
            $query->execute();
            return $res->withStatus(200);
        } catch ( \PDOException $e ) {
            error_log($e);
            return $res->withStatus(400);
        }
    }
    public function modify( Request $req, Response $res, Array $args ) {
        $userId = $this->getSessionUserId();
        if ( $userId === null )
            return $res->withStatus(401);
        $reqBoardNo = $req->getParam("boardNo");
        $reqBoardSubject = $req->getParam("boardSubject");
        $reqBoardContext = $req->getParam("boardContext");
        $query = $this->container->db->prepare('UPDATE BOARD SET SUBJECT = :subject, CONTENT = :context WHERE NO = :boarNo');
        $query->bindParam(':subject', $reqBoardSubject, \PDO::PARAM_STR);
        $query->bindParam(':context', $reqBoardContext, \PDO::PARAM_STR);
        $query->bindParam(':boarNo', $reqBoardNo, \PDO::PARAM_INT);
        try {
            $query->execute();
            return $res->withStatus(200);
        } catch ( \PDOException $e ) {
            error_log($e);
            return $res->withStatus(400);
        }
    }
    public function delete( Request $req, Response $res, Array $args ) {
        $userId = $this->getSessionUserId();
        if ( userId === null )
            return $res->withStatus(401);
        $reqBoardNo = $req->getParam("boardNo");
        $query = $this->container->db->prepare('SELECT *  FROM BOARD WHERE NO = :boardNo');
        $query->bindParam(':boardNo', $reqBoardNo, \PDO::PARAM_INT);
        try {
            $query->execute();
            $data = $query->fetchAll();
            if ( sizeof($data) != 1 )
                throw new \PDOException('no row found');
            if ( $data[0]['user_id'] !== $userId )
                return $res->withStatus(400);
            $query = $this->container->db->prepare('DELETE FROM BOARD WHERE NO = :boardNo AND USER_ID = :userId');
            $query->bindParam(':boardNo', $reqBoardNo, \PDO::PARAM_INT);
            $query->bindParam(':userId', $userId, \PDO::PARAM_STR);
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