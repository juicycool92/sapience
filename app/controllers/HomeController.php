<?php

namespace App\controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController {
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }
    public function index( Request $req, Response $res, Array $args ){
        return $this->container->view->render($res, 'home.twig');
    }
}