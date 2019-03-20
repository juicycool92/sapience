<?php
namespace App\controllers;

use Interop\Container\ContainerInterface;

class BoardController {
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }
    public function index($req, $res){
        return $this->container->view->render($res, 'board/list.twig');
    }
}