<?php

/**
 * Routes.
 *
 * This file contains the route definitons for Kolibri.
 * If you extend the functionality of Kolibri and add methods
 * which you want to call with HTTP, you need to add them to
 * this file.
 *
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 *
 * @since     May 2013
 *
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
$router = new \Phalcon\Mvc\Router();

$router->removeExtraSlashes(true);

$router->setDefaultController('kolibri');
$router->setDefaultAction('index');

$router->add(
    '/{title:([a-zA-Z0-9_-]+)}',
    [
        'controller' => 'kolibri',
        'action'     => 'page',
    ]
);
$router->add(
    '/page/{title:([a-zA-Z0-9_-]+)}',
    [
        'controller' => 'kolibri',
        'action'     => 'page',
    ]
);
$router->add(
    '/edit/:int',
    [
        'controller' => 'kolibri',
        'action'     => 'edit',
        'id'         => 1,
    ]
);
$router->addPost(
    '/update',
    [
        'controller' => 'kolibri',
        'action'     => 'update',
    ]
);
$router->add(
    '/create',
    [
        'controller' => 'kolibri',
        'action'     => 'create',
    ]
);
$router->addPost(
    '/save',
    [
        'controller' => 'kolibri',
        'action'     => 'save',
    ]
);
$router->add(
    '/list',
    [
        'controller' => 'kolibri',
        'action'     => 'list',
    ]
);
$router->add(
    '/delete/:int',
    [
        'controller' => 'kolibri',
        'action'     => 'delete',
        'id'         => 1,
    ]
);
$router->add(
    '/version/:int',
    [
        'controller' => 'kolibri',
        'action'     => 'version',
        'id'         => 1,
    ]
);
$router->add(
    '/version/:int/:int',
    [
        'controller' => 'kolibri',
        'action'     => 'diff',
        'id'         => 1,
        'revision'   => 2,
    ]
);
$router->add(
    '/changes',
    [
        'controller' => 'kolibri',
        'action'     => 'changes',
    ]
);
$router->add(
    '/export/:int',
    [
        'controller' => 'kolibri',
        'action'     => 'export',
        'id'         => 1,
    ]
);
$router->add(
    '/reimport/:int/:int',
    [
        'controller' => 'kolibri',
        'action'     => 'reimport',
        'id'         => 1,
        'revision'   => 2,
    ]
);

return $router;
