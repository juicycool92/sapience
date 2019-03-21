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
        $this->chunkSize = 10;
    }

    public function selectListByLimit(Request $req, Response $res, Array $args){
        error_log("ola");
        var_dump($this->container);
        error_log(json_encode($this->container->db));
        $temp = $this->container->db->query("select * from board")->fetchAll(PDO::FETCH_OBJ);
        #$idx = $req->getParam("boardIdx");
        #$resultData = $this->selectBoardLimit($idx);
        #$res->withJson($resultData);
        #$res->withStatus(200);
//
//        error_log($data);

        error_log($temp);
//        echo(json_encode($temp));
        #$temp = $this->container-> db->query("select * from board")->fetchAll($this->PDO::FETCH_OBJ);
        #error_log($temp);
        #$returnRes = clone $res;
        #$returnRes->withStatus(200);
        #$returnRes->withJson("a",$args);
        #return $returnRes;
    }

    protected function selectBoardLimit($idx) {
        error_log("hello");
        $query = $this->db->query("SELECT * FROM BOARD");
        error_log("hello");
        $query->execute(array(':idx'=> $idx, ':size'=> $this->chunkSize ));
        error_log("hello");
        $data = $query->fetchAll(\PDO::FETCH_OBJ);
        error_log("hello");
        return $data;
    }
}