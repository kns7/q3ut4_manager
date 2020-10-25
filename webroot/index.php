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

$app->get('/daemon/notificationsTelegram',function() use($app){
    $tmpstorage = dirname(__FILE__)."/../app/config/laststate.tmp";
    if(!file_exists($tmpstorage)){
        $laststate = [];
    }else{
        $laststate = json_decode(file_get_contents($tmpstorage));
    }
    // Storage File
    $storage = fopen($tmpstorage,"w");

    $players = $app->Ctrl->RCON->getPlayers();
    fwrite($storage,json_encode($players));

    if(is_array($laststate)) {
        if($_SERVER['SITE_MODE']  == "development"){ echo "Last State exists<br/>"; }
        if (count($laststate) == 0 && count($players) > 0) {
            $list = "";
            foreach ($players as $p) {
                $list .= "- _".$p['name'] . "_\n";
            }
            $msg = "*Hey!*\nIl y a du monde sur le serveur, viens donc nous rejoindre\n$list";
            if($_SERVER['SITE_MODE']  == "development"){ echo "Sending Telegram Message:<br/><pre>$msg</pre>"; }
            $return = $app->Ctrl->RCON->sendNotificationTelegram($msg);
            if($_SERVER['SITE_MODE']  == "development"){
                echo "<pre>";
                var_dump($return);
                echo "</pre>";
            }
        }else{
            if($_SERVER['SITE_MODE']  == "development"){ echo "Same results as previous test: ". count($players)." player(s) online<br/>";  }
        }
    }
});

if($app->Ctrl->Auth->isauth()){
    $app->get('/logout',function() use($app){
        $app->Ctrl->Auth->logout();
        $app->redirect($app->urlFor('login'));
    });

    $app->get('/mapcycle-editor',function() use($app){
        $maps = $app->Ctrl->Maps->getList();
        $mapcycle = $app->Ctrl->Maps->getMapCycle();
        $app->render('mapcycle-editor.php',compact('app','maps','mapcycle'));
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
        $app->get("/status",function() use($app){
            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json; charset=utf-8');
            $status = $app->Ctrl->RCON->getStatus();
            $players = $app->Ctrl->RCON->getPlayers();
            echo json_encode((object)[
                "mapname" => $status->map->getName(),
                "mapimg" => $status->map->getImg(),
                "mapsize" => $status->map->getSize(),
                "timelimit" => $status->cvars["timelimit"],
                "roundtime" => $status->cvars["g_roundtime"],
                "gametypename" => $status->gametype->getName(),
                "gametypedescription" => $status->gametype->getDescription(),
                "players" => $players
            ]);
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

            $app->post('/mapcycleEdit',function() use($app){
                $app->response->setStatus(200);
                $app->response()->headers->set('Content-Type','application/json; charset=utf-8');
                $app->Ctrl->Maps->setMapCycle($_POST['maps']);
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

        $app->get('/getMapCycle',function() use($app){
            echo "<pre>";
            var_dump($app->Ctrl->Maps->getMapCycle());
            echo "</pre>";
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

        $app->get('/blabla',function() use($app){
            $app->Ctrl->RCON->sendMessage("Salut");
            sleep(1);
            $app->Ctrl->RCON->sendMessage("Comment");
            sleep(1);
            $app->Ctrl->RCON->sendMessage("ca");
            sleep(1);
            $app->Ctrl->RCON->sendMessage("va");
            sleep(1);
        });

        $app->get('/players',function() use($app){
            echo "<pre>";
            var_dump($app->Ctrl->RCON->getPlayers());
            echo "</pre>";
        });

        $app->get('/sendTelegram',function() use($app){
            $app->Ctrl->Auth->sendNotificationTelegram("Serialg vient de se connecter");
        });
    });
}

$app->run();