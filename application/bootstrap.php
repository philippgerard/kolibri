<?php

/**
 * Kolibri bootstrap.
 *
 * This bootstrap defines all dependencies of Kolibri and applies
 * the relevant settings from the config.
 *
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 *
 * @since     May 2013
 *
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
namespace Kolibri;

try {
    $config = new \Phalcon\Config(require('config/config.php'));

    $loader = new \Phalcon\Loader();

    require $config->application->vendorDir.'autoload.php';

    $namespaces = [
        'Kolibri\Controllers' => $config->application->controllersDir,
        'Kolibri\Models'      => $config->application->modelsDir,
        'Kolibri\Library'     => $config->application->libraryDir,
        'Kolibri\Forms'       => $config->application->formsDir,
        'Kolibri\Plugins'     => $config->application->pluginsDir,
    ];

    $loader->registerNamespaces($namespaces);
    $loader->register();

    $di = new \Phalcon\DI\FactoryDefault();

    $di->set('config', $config);

    $di->set(
        'view',
        function () use ($config, $di) {
            $view = new \Phalcon\Mvc\View();

            $eventsManager = $di->getShared('eventsManager');
            if ($config->volt->tidy === true) {
                $eventsManager->attach('view:afterRender', new \Kolibri\Plugins\Tidy());
            }

            $view->setViewsDir($config->application->viewsDir);
            $view->setEventsManager($eventsManager);
            $view->registerEngines(
                [
                    '.phtml' => 'volt',
                ]
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
                function () use ($compiler) {
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
            return new \Kolibri\Library\Flash();
        }
    );

    $di->set(
        'viewCache',
        function () use ($config) {
            $frontCache = new \Phalcon\Cache\Frontend\Output([
                'lifetime' => $config->cache->views->lifetime,
            ]);
            $adapter = "\Phalcon\Cache\Backend\\".ucfirst($config->cache->views->adapter);
            $cache = new $adapter($frontCache, $config->cache->views->settings->toArray());

            return $cache;
        }
    );

    $di->set(
        'modelsCache',
        function () use ($config) {
            $frontCache = new \Phalcon\Cache\Frontend\Data([
                'lifetime' => $config->cache->models->lifetime,
            ]);
            $adapter = "\Phalcon\Cache\Backend\\".ucfirst($config->cache->models->adapter);
            $cache = new $adapter($frontCache, $config->cache->models->settings->toArray());

            return $cache;
        }
    );

    $di->set(
        'router',
        function () {
            return require 'config/routes.php';
        }
    );

    $di->set(
        'dispatcher',
        function () {
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace('Kolibri\Controllers\\');

            return $dispatcher;
        }
    );

    $di->set(
        'db',
        function () use ($config) {
            $adapter = '\Phalcon\Db\Adapter\Pdo\\'.ucfirst($config->database->adapter);

            return new $adapter($config->database->config->toArray());
        }
    );

    $di->set('markdown', new \Michelf\MarkdownExtra());

    $di->set(
        'purifier',
        function () {
            $config = \HTMLPurifier_Config::createDefault();
            $purifier = new \HTMLPurifier($config);

            return $purifier;
        }
    );

    $application = new \Phalcon\Mvc\Application();
    $application->setDI($di);

    echo $application->handle()->getContent();
} catch (\Exception $e) {
    echo $e->getMessage();
}
