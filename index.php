<?php
use App\controllers\HomeController;
use App\controllers\BoardController;
use App\controllers\BoardAjaxController;

require 'vendor/autoload.php';

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true, //display error detail on logger, disabled require when server live.
    ]
]);

$container = $app->getContainer();
$container['greet'] = function() {
    return 'hello from container';
};
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=192.168.0.222;dbname=temp' , 'storeuser', 'storeuser!');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__.'\resources\views', [
        'cache' => false,
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

    return $view;
};
//$container['BoardController'] = function ($container) {
//    $view = $container->get('view');
//    return new BoardController($view);
//};

$app->get('/', HomeController::class.':index');

$app->get('/home', function($req,$res) {
    $temp = $this->db->query("select * from board")->fetchAll(PDO::FETCH_OBJ);
    error_log($temp);
    echo(json_encode($temp));
    //return $this->view->render($res, 'home.twig');

});

$app->group('/board',function () {
    #$temp = $this->db->query("select * from board")->fetchAll(PDO::FETCH_OBJ);
    #error_log($temp);
    $this->group('/ajax', function () {
        $this->post('/selectListByLimit', BoardAjaxController::class .':selectListByLimit');
        $this->delete('/delete', BoardAjaxController::class .':delete');
        $this->post('/insert', BoardAjaxController::class .':insert');
        $this->post('/modify', BoardAjaxController::class .':modify');
    });
    $this->get('',BoardController::class.':index');
    $this->get('/detail/{board_no}',BoardController::class.':index');
});

$app->get('/errorStarter', function() {
    echo $this->nothing;
} );

$app->get('/users', function($req,$res) {
    $user = [
        [
            'userName' => 'billy',
            'name' => 'Billy billy',
            'email' => 'billy@billy.com'
        ],
        [
            'userName' => 'chally',
            'name' => 'chally chally',
            'email' => 'chally@chally.com'
        ]
    ];
    return $this->view->render($res, 'users.twig', [
        'users' => $user,
    ]);
} );
$app->run();

