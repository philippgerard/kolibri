<?php

namespace Kolibri\Library;

/**
 * Base Controller.
 *
 * This is the base controller which is used as a boilerplate for
 * all other controllers Kolibri has or might have in the future.
 *
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 *
 * @since     May 2013
 *
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
        $this->assets
            ->collection('css')
            ->setTargetPath('cache/styles.css')
            ->setTargetUri('cache/styles.css')
            ->addCss('css/normalize.css')
            ->addCss('css/bootstrap.css')
            ->addCss('css/bootstrap-theme.css')
            ->addCss('css/app.css')
            ->join(true)
            ->addFilter(new \Phalcon\Assets\Filters\Cssmin());
        $this->assets
            ->collection('javascript')
            ->setTargetPath('cache/scripts.js')
            ->setTargetUri('cache/scripts.js')
            ->addJs('scripts/vendor/modernizr.js')
            ->addJs('scripts/vendor/jquery.js')
            ->addJs('scripts/bootstrap.min.js')
            ->addJs('scripts/jwerty.min.js')
            ->addJs('scripts/app.js')
            ->join(true)
            ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
    }

    public function beforeExecuteRoute(\Phalcon\MVC\Dispatcher $dispatcher)
    {
        if ($this->config->auth->enabled) {
            if (!$this->persistent->authenticated && $dispatcher->getActionName() != 'login') {
                $dispatcher->forward(['action' => 'login']);
            }
        }
    }
}
