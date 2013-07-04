<?php

namespace Kolibri\Library;

/**
 * Base Controller
 *
 * This is the base controller which is used as a boilerplate for
 * all other controllers Kolibri has or might have in the future.
 *
 * @package   Kolibri\Library
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @since     May 2013
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 *
 * @property \Phalcon\View           view
 * @property \Phalcon\Assets\Manager assets
 */
class Controller extends \Phalcon\Mvc\Controller
{

    public function initialize()
    {
        $this->view->setTemplateAfter('common');
        $this->assets->addCss('css/normalize.css')
            ->addCss('css/foundation.css')
            ->addCss('css/app.css');
        $this->assets->addJs('scripts/vendor/custom.modernizr.js')
            ->addJs('scripts/vendor/zepto.js')
            ->addJs('scripts/foundation.min.js')
            ->addJs('scripts/jwerty.min.js')
            ->addJs('scripts/app.min.js');
    }

    public function beforeExecuteRoute(\Phalcon\MVC\Dispatcher $dispatcher)
    {
        if ($this->config->auth->enabled) {
            if (!$this->persistent->authenticated && $dispatcher->getActionName() != 'login') {
                $dispatcher->forward(array('action' => 'login'));
            }
        }
    }

}
