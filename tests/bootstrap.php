<?php

define("APPLICATION_PATH", realpath(
    __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application'
) . DIRECTORY_SEPARATOR);

$config = new \Phalcon\Config(require_once(APPLICATION_PATH . "config/config.php"));

$loader = new \Phalcon\Loader();

require $config->application->vendorDir . 'autoload.php';

$namespaces = array(
    'Wiki\Controllers' => $config->application->controllersDir,
    'Wiki\Models'      => $config->application->modelsDir,
    'Wiki\Library'     => $config->application->libraryDir,
    'Wiki\Forms'       => $config->application->formsDir,
    'Wiki\Plugins'     => $config->application->pluginsDir
);

$loader->registerNamespaces($namespaces);
$loader->register();

$di = new \Phalcon\DI\FactoryDefault();

$di->set(
    'view',
    function () use ($config, $di) {
        $view = new \Phalcon\Mvc\View();

        $eventsManager = $di->getShared('eventsManager');
        //$eventsManager->attach("view:afterRender", new \Wiki\Plugins\Tidy());

        $view->setViewsDir($config->application->viewsDir);
        $view->setEventsManager($eventsManager);
        $view->registerEngines(
            array(
                ".phtml" => 'volt'
            )
        );

        return $view;
    }
);

$di->set(
    'volt',
    function ($view, $di) use ($config) {

        $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

        $volt->setOptions($config->volt->toArray());
        $compiler = $volt->getCompiler();
        $compiler->addFunction(
            'memory',
            function ($resolvedArgs, $exprArgs) use ($compiler) {
                return "round(memory_get_usage(false)/1024,1).'kB'";
            }
        );
        return $volt;
    },
    true
);

$di->set(
    'session',
    function () {
        $session = new \Phalcon\Session\Adapter\Files();
        $session->start();
        return $session;
    }
);

$di->set(
    'flash',
    function () {
        return new \Wiki\Library\Flash();
    }
);

$di->set(
    'viewCache',
    function () {
        $frontCache = new \Phalcon\Cache\Frontend\Output(array(
            "lifetime" => 3600
        ));
        $cache      = new \Phalcon\Cache\Backend\Apc($frontCache, array());

        return $cache;
    }
);

$di->set(
    'router',
    function () {
        return require_once("config/routes.php");
    }
);

$di->set(
    'dispatcher',
    function () {
        $dispatcher = new \Phalcon\Mvc\Dispatcher();
        $dispatcher->setDefaultNamespace('Wiki\Controllers\\');

        return $dispatcher;
    }
);

$di->set(
    'db',
    function () use ($config) {
        return new \Phalcon\Db\Adapter\Pdo\Sqlite($config->database->toArray());
    }
);

$di->set('markdown', new \Michelf\MarkdownExtra());

$application = new \Phalcon\Mvc\Application();
$application->setDI($di);