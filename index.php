<?php
use App\controllers\HomeController;
use App\controllers\BoardController;
use App\controllers\BoardAjaxController;
use App\controllers\UserAjaxController;
use App\controllers\UserController;
use Slim\Http\Request;
use Slim\Http\Response;

require 'vendor/autoload.php';

session_cache_limiter(false);
session_start();

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
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=test' , 'root', '0122');
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

$auth = function (Request $req, Response $res, $next) use ($container) {
  if ( !isset($_SESSION['user_id']) ) {
      $res = $res->withRedirect( $container->router->pathFor('home') );
  } else {
      error_log($_SESSION['user_id']);
  }
  return $next( $req,$res );
};

$app->get('/', HomeController::class.':index')->add($auth);

$app->get('/home', function($req,$res) {
    return $this->view->render($res, 'home.twig');
})->setName("home");

$app->group('/board',function () use($auth) {
    $this->group('/ajax', function () {
        $this->post('/selectListByLimit', BoardAjaxController::class .':selectListByLimit');
        $this->post('/selectListBackByLimit', BoardAjaxController::class .':selectListBackByLimit');
        $this->delete('/delete', BoardAjaxController::class .':delete');
        $this->post('/insert', BoardAjaxController::class .':insert');
        $this->post('/modify', BoardAjaxController::class .':modify');
        $this->get('/test', BoardAjaxController::class.':test');
    });
    $this->get('',BoardController::class.':index')->setName("board");
    $this->get('/modify/{boardNo}', BoardController::class .':modify')->add($auth);
    $this->get('/write', BoardController::class.':write')->add($auth);
    $this->get('/detail/{board_no}',BoardController::class.':detail');
});

$app->group('/user',function () use($auth) {
    $this->group('/ajax', function () {
        $this->post('/signIn', UserAjaxController::class .':signIn');
        $this->post('/modify', UserAjaxController::class .':modify');
    });
    $this->get('/modify/{user_id}', UserController::class .':modify')->add($auth);
    $this->get('/logOut', UserController::class .':logOut');
    $this->post('/login', UserController::class .':login');
    $this->get('/{user_id}',UserController::class.':detail');

});

$app->run();

