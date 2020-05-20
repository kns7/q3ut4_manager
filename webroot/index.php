<?php

use App\Controller\AuthController;
use App\Controller\GametypesController;
use App\Controller\MapsController;
use App\Controller\RCONController;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Propel;
use Slim\Slim;

session_start();

require "../vendor/autoload.php";

// MySQL Configuration / Connection
$serviceContainer = Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('default', 'mysql');
$manager = new ConnectionManagerSingle();
$manager->setConfiguration(array (
    'classname' => 'Propel\\Runtime\\Connection\\DebugPDO',
    'dsn' => 'mysql:host='.$_SERVER['MYSQL_HOST'].';port='.$_SERVER['MYSQL_PORT'].';dbname='.$_SERVER['MYSQL_DB_VMAIL'],
    'user' => $_SERVER['MYSQL_USER'],
    'password' => $_SERVER['MYSQL_PASSWORD'],
    'attributes' =>
        array (
            'ATTR_EMULATE_PREPARES' => false,
            'ATTR_TIMEOUT' => 30,
        )
));
$manager->setName('vmail');
$serviceContainer->setConnectionManager('default', $manager);

$sitemode = (isset($_SERVER['SITE_MODE']))?$_SERVER['SITE_MODE']:'production';


$app = new Slim([
    'template.path' => 'templates/',
    'mode' => $sitemode
]);

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

// Dependency Injections
$app->container->singleton('Ctrl',function() use($app){
    return (object)[
        'Maps' => new MapsController($app),
        'Gametypes' => new GametypesController($app),
        'RCON' => new RCONController($app),
        'Auth' => new AuthController($app)
    ];
});

// Routes
$app->get('/', function() use($app){
    if($app->Ctrl->Auth->isauth()){

    }else{
        $app->redirect($app->urlFor(('login')));
    }
})->name("root");


// Authentication Routes
$app->get('/login',function() use($app){
    $app->render('login.php',compact('app'));
})->name("login");

$app->post('/login',function() use($app){
    if($app->Ctrl->Auth->auth($_POST['password'])){
        $app->flash('success','Bienvenue');
        $app->redirect('/');
    }else{
        $app->flash('error','Mauvais Mot de passe!');
        $app->redirect($app->urlFor('login'));
    }
});

$app->get('/logout',function() use($app){
    $app->Ctrl->Auth->logout();
    $app->redirect($app->urlFor('login'));
});