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
require "../app/Controller/q3rcon.php";

// MySQL Configuration / Connection
$serviceContainer = Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('default', 'mysql');
$manager = new ConnectionManagerSingle();
$manager->setConfiguration(array (
    'classname' => 'Propel\\Runtime\\Connection\\DebugPDO',
    'dsn' => 'mysql:host='.$_SERVER['MYSQL_HOST'].';port='.$_SERVER['MYSQL_PORT'].';dbname='.$_SERVER['MYSQL_DB'],
    'user' => $_SERVER['MYSQL_USER'],
    'password' => $_SERVER['MYSQL_PASSWORD'],
    'settings' =>
        array (
            'charset' => 'utf8'
        )
));
$manager->setName('default');
$serviceContainer->setConnectionManager('default', $manager);
$serviceContainer->setDefaultDatasource('default');


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
        'log.enable' => true,
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
        $players = $app->Ctrl->RCON->getPlayers();
        $status = $app->Ctrl->RCON->getStatus();
        $app->render('home.php',compact('app','status','players'));
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

if($app->Ctrl->Auth->isauth()){
    $app->get('/logout',function() use($app){
        $app->Ctrl->Auth->logout();
        $app->redirect($app->urlFor('login'));
    });

    // Ajax Requests
    $app->group('/ajax',function() use($app){
        $app->get('/settings',function() use($app){
            $maps = $app->Ctrl->Maps->getList();
            $gametypes = $app->Ctrl->Gametypes->getList();
            $cvars = $app->Ctrl->RCON->getCvarList();
            $actual = [
                'map' => $app->Ctrl->Maps->getByFile($cvars['mapname']),
                'gametype' => $app->Ctrl->Gametypes->getByCode($cvars['g_gametype'])
            ];
            $app->render('settings.php',compact('app','maps','gametypes','cvars','actual'));
        });
        $app->get('/gametype-desc/:id',function($id) use($app){
            $gametype = $app->Ctrl->Gametypes->get($id);
            if(!is_null($gametype)){
                echo $gametype->getDescription();
            }
        });

        $app->group('/action',function() use($app){
            $app->post('/reload',function() use($app){
                $app->response->setStatus(200);
                $app->Ctrl->RCON->serverReload();
            });

            $app->post('/saveParams',function() use($app){
                $app->response->setStatus(200);
                $app->response()->headers->set('Content-Type', 'application/json; charset=utf-8');
                echo json_encode($app->Ctrl->RCON->saveParams($_POST));
            });
        });
    });
}


// Tests Routes
if($app->getMode() == "development"){
    $app->group('/test',function() use($app){
        $app->get('/getMap', function() use($app){
            $app->Ctrl->RCON->getMap();
        });

        $app->get('/config',function() use($app){
            echo "<pre>";
            var_dump($app->Ctrl->Auth->getConfig());
            echo "</pre>";
        });

        $app->get('/cvars',function() use($app){
            echo "<pre>";
            var_dump($app->Ctrl->RCON->getCvarList());
            echo "</pre>";
        });

        $app->get('/players',function() use($app){
            echo "<pre>";
            var_dump($app->Ctrl->RCON->getPlayers());
            echo "</pre>";
        });
    });
}

$app->run();