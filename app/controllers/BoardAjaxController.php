<?php
namespace App\controllers;
use Slim\Http\Request;
use Slim\Http\Response;

class BoardAjaxController {


    public function selectListByLimit(Request $req, Response $res, Array $args){
        $data = $req->getParsedBody();
        echo $data['boardIdx'];
        #$returnRes = clone $res;
        #$returnRes->withStatus(200);
        #$returnRes->withJson("a",$args);
        #return $returnRes;
    }
}