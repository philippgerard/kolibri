<?php

namespace Kolibri\Controllers;

use Kolibri\Library\Controller as Controller;
use Kolibri\Models\Pages;
use Kolibri\Models\Versions;

/**
 * KolibiController
 *
 * This controller is the home of essentially all core Kolibri features.
 *
 * @package   Kolibri\Controllers
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @since     May 2013
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
class KolibriController extends Controller
{

    /**
     * Renders the main page
     */
    public function indexAction()
    {
        $this->dispatcher->setParam('title', 'Index');
        $this->dispatcher->forward(array('action' => 'page'));
    }

    /**
     * Login for the user, throttles the login attempts if need be
     *
     * @return mixed
     */
    public function loginAction()
    {
        // Make sure also login pages are styled
        $this->initialize();
        
        $this->view->title = 'Login';
        if ($_POST) {
            if (isset($this->persistent->attempts) && $this->persistent->attempts >= 3 && $this->persistent->attempts < 10) {
                sleep($this->persistent->attempts);
            } elseif (isset($this->persistent->attempts) && $this->persistent->attempts >= 10) {
                return $this->response->resetHeaders()
                    ->setStatusCode(404, null)
                    ->setContent('');
            }
            $filter   = new \Phalcon\Filter();
            $password = $filter->sanitize($_POST['password'], array('trim'));
            if ($password == $this->config->auth->password) {
                $this->persistent->authenticated = true;
                $this->flash->success('You are now authenticated.');
                return $this->response->redirect("index");
            } else {
                if (!isset($this->persistent->attempts)) {
                    $this->persistent->attempts = 1;
                } else {
                    $this->persistent->attempts++;
                }
            }
        }
    }

    /**
     * Renders a specific wiki page
     */
    public function pageAction()
    {
        $this->assets->addJs('scripts/highlight.min.js');
        $page = Pages::find(
            array(
                'conditions' => "title = :title:",
                'bind'       => array('title' => $this->dispatcher->getParam('title')),
                'cache'      => array('key' => 'page-' . $this->dispatcher->getParam('title')),
            )
        );
        if (!isset($page[0])) {
            $this->view->page  = array();
            $this->view->title = 'New page';
        } else {
            $this->view->page  = $page[0];
            $this->view->title = $page[0]->title;
        }
    }

    /**
     * Shows the edit dialogue for a specific page
     */
    public function editAction()
    {
        $page = Pages::findFirst(
            array(
                'conditions' => "id = :id:",
                'bind'       => array('id' => (int)$this->dispatcher->getParam('id'))
            )
        );
        if ($page === false) {
            return $this->dispatcher->forward(array('action' => 'error404'));
        }
        $this->assets
            ->collection("ace")
            ->addJs('scripts/ace/ace.js');
        $page->content     = htmlentities($page->content);
        $this->view->page  = $page;
        $this->view->title = $page->title . ' – Edit ';
        $editable          = new \stdClass();
        $editable->content = $page->content;
        $editable->id      = $page->id;
        $this->view->form  = new \Kolibri\Forms\Edit($editable);
    }

    /**
     * Page not found action
     */
    public function error404Action()
    {
        $this->view->title = 'Error';
    }

    /**
     * Lists all pages
     */
    public function listAction()
    {
        $pages = Pages::find(
            array(
                'order' => 'title ASC'
            )
        );
        $result = array();
        foreach($pages as $page) {
            $result[substr($page->title, 0,1)][] = $page;
        }
        $this->view->pages = $result;
        $this->view->title = 'List of all pages';
    }

    /**
     * Deletes a page
     *
     * @todo Make use of Phalcon relations. If only they worked as expected...
     */
    public function deleteAction()
    {
        $id   = (int)$this->dispatcher->getParam('id');
        $page = Pages::findFirst($id);
        if ($page === false) {
            return $this->dispatcher->forward(array('action' => 'error404'));
        }
        $page->getVersions()->delete();
        $page->delete();
        $this->viewCache->delete('page-' . $page->id);
        $this->modelsCache->delete('page-' . $page->title);
        $this->flash->success('The page has been deleted!');
        return $this->response->redirect('');
    }

    /**
     * Offers a form to create a new page
     */
    public function createAction()
    {
        $this->assets
            ->collection("ace")
            ->addJs('scripts/ace/ace.js');
        $this->view->form  = new \Kolibri\Forms\Create();
        $this->view->title = 'Add page';
    }

    /**
     * Saves a new page
     */
    public function saveAction()
    {
        $form = new \Kolibri\Forms\Create();
        if (!$form->isValid($_POST)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array('action' => 'create'));
        } else {
            $filter        = new \Phalcon\Filter();
            $page          = new Pages();
            $page->content = $filter->sanitize($_POST['content'], array('trim'));
            $page->title   = $filter->sanitize($_POST['title'], array('string', 'trim'));
            $page->create();
            $version          = new Versions();
            $version->page_id = $page->id;
            $version->content = $page->content;
            $version->version = 1;
            $version->create();
            $this->viewCache->delete('page-' . $page->id);
            $this->modelsCache->delete('page-' . $page->title);
            $this->flash->success("The page has been created!");
            return $this->response->redirect("page/" . $page->title);
        }
    }

    /**
     * Saves changes to a specific page
     */
    public function updateAction()
    {
        $form = new \Kolibri\Forms\Edit();
        $id   = (int)$_POST['id'];
        if (!$form->isValid($_POST)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            $this->dispatcher->setParam('id', $id);
            return $this->dispatcher->forward(array('action' => 'edit'));
        } else {
            $filter = new \Phalcon\Filter();
            $page   = Pages::findFirst($id);
            if ($page === false) {
                return $this->dispatcher->forward(array('action' => 'error404'));
            }
            $page->content = $filter->sanitize($_POST['content'], array('trim'));
            $page->update();
            $curVersion       = Versions::maximum(
                array(
                    "column"     => "version",
                    "conditions" => "page_id = :id:",
                    "bind"       => array('id' => $id)
                )
            );
            $version          = new Versions();
            $version->page_id = $page->id;
            $version->content = $page->content;
            $version->version = $curVersion + 1;
            $version->create();
            $this->viewCache->delete('page-' . $page->id);
            $this->modelsCache->delete('page-' . $page->title);
            $this->flash->success("The changes have been saved!");
            return $this->response->redirect("page/" . $page->title);
        }
    }

    /**
     * Lists all available revisions
     */
    public function versionAction()
    {
        $id   = (int)$this->dispatcher->getParam('id');
        $page = Pages::findFirst(
            array(
                'conditions' => "id = :id:",
                'bind'       => array('id' => $id)
            )
        );
        if ($page === false) {
            return $this->dispatcher->forward(array('action' => 'error404'));
        }
        $versions             = Versions::find(
            array(
                'page_id = :id:',
                'bind' => array(
                    'id' => $id,
                )
            )
        );
        $this->view->page     = $page;
        $this->view->versions = $versions;
        $this->view->title    = 'Revisions – ' . $page->title;
    }

    /**
     * Shows a visual diff of what has changed
     */
    public function diffAction()
    {
        $id       = (int)$this->dispatcher->getParam('id');
        $revision = (int)$this->dispatcher->getParam('revision');

        $diffVersion = Versions::findFirst(
            array(
                'page_id = :id: AND version = :revision:',
                'bind' => array(
                    'id'       => $id,
                    'revision' => $revision
                )
            )
        );
        if ($diffVersion === false) {
            return $this->dispatcher->forward(array('action' => 'error404'));
        }
        $currentVersion = Pages::findFirst($id);

        $diffEngine = new \Diff(explode("\n", $diffVersion->content), explode("\n", $currentVersion->content), []);

        $renderer = new \Diff_Renderer_Html_SideBySide;

        $this->view->diff     = $diffEngine->render($renderer);
        $this->view->page     = $currentVersion;
        $this->view->revision = $diffVersion->version;
        $this->view->title    = "diff – " . $currentVersion->title;
    }

    /**
     * Displays the latest 10 changes
     */
    public function changesAction()
    {
        $versions = Versions::find(
            array(
                'limit' => 10,
                'order' => 'created DESC',
            )
        );
        $pages    = array();
        if (!empty($versions)) {
            foreach ($versions as $version) {
                $page            = Pages::findFirst($version->page_id);
                $result          = new \stdClass();
                $result->id      = $page->id;
                $result->title   = $page->title;
                $result->version = $version->version;
                $result->created = $version->created;
                $pages[]         = $result;
            }
        }
        $this->view->pages = $pages;
        $this->view->title = 'Latest changes';
    }

    /**
     * Export a wiki page as a markdown file (.md)
     */
    public function exportAction()
    {
        $page = Pages::findFirst(
            array(
                'conditions' => "id = :id:",
                'bind'       => array('id' => $this->dispatcher->getParam('id'))
            )
        );
        if (!$page) {
            return $this->dispatcher->forward(array('action' => 'error404'));
        } else {
            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $page->title . '.md"');
            $this->response->setHeader('Content-Length', mb_strlen($page->content, '8bit'));
            $this->response->setContentType('x-markdown; charset=UTF-8');
            $this->response->setContent($page->content);
            return $this->response;
        }
    }

    /**
     * Reimports an old revision, creating a new revision with the old contents of the old revision in the process.
     */
    public function reimportAction()
    {
        $id       = (int)$this->dispatcher->getParam('id');
        $revision = (int)$this->dispatcher->getParam('revision');

        $oldRevision = Versions::findFirst(
            array(
                'page_id = :id: AND version = :revision:',
                'bind' => array(
                    'id'       => $id,
                    'revision' => $revision
                )
            )
        );
        $page        = Pages::findFirst($id);
        if ($page === false) {
            return $this->dispatcher->forward(array('action' => 'error404'));
        }
        if ($oldRevision === false) {
            return $this->dispatcher->forward(array('action' => 'error404'));
        }
        $filter        = new \Phalcon\Filter();
        $page->content = $filter->sanitize($oldRevision->content, array('trim'));
        $page->update();
        $curVersion       = Versions::maximum(
            array(
                "column"     => "version",
                "conditions" => "page_id = :id:",
                "bind"       => array('id' => $id)
            )
        );
        $version          = new Versions();
        $version->page_id = $page->id;
        $version->content = $page->content;
        $version->version = $curVersion + 1;
        $version->create();
        $this->viewCache->delete('page-' . $page->id);
        $this->modelsCache->delete('page-' . $page->title);
        $this->flash->success("The changes have been saved!");
        return $this->response->redirect("page/" . $page->title);
    }

}
