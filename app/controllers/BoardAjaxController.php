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

    public function test($req, $res, Array $args){
        error_log("ola");
        $a = $this->container->db->query('select * from board LIMIT 0,5')->fetchAll(5);
        error_log("ola2");
        var_dump(json_encode($a));
    }
    public function selectListByLimit(Request $req, Response $res, Array $args){
        //error_log("ola");
        //$temp = $this->container->db->query("select * from board limit 0,5")->fetchAll(5);
        //error_log(json_encode($temp));
        $idx = $req->getParam("boardIdx");
        $resultData = $this->selectBoardLimit($idx);
        error_log(json_encode($resultData));
        $resultData = $resultData->fetchAll(5);
        error_log(json_encode($resultData));
        error_log('EOL');
        #$res->withJson($resultData);
        #$res->withStatus(200);
//
//        error_log($data);

//        error_log($temp);
//        echo(json_encode($temp));
        #$temp = $this->container-> db->query("select * from board")->fetchAll($this->PDO::FETCH_OBJ);
        #error_log($temp);
        #$returnRes = clone $res;
        #$returnRes->withStatus(200);
        #$returnRes->withJson("a",$args);
        #return $returnRes;
    }

    protected function selectBoardLimit($idx) {
        error_log('a');
        $query = $this->container->db->prepare('SELECT * FROM BOARD LIMIT :idx , :sizee;');
        error_log('b');
        $query->execute( array(':idx' => $idx, ':sizee' => $this->chunkSize ) );
        error_log('c');
        $data = $query->fetchAll(\PDO::FETCH_OBJ);
        error_log('d');
        return $data;
    }
}