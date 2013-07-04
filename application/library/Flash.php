<?php

namespace Kolibri\Library;

/**
 * Flash message transport
 *
 * This is just a dirty hack to "dumb down" the Phalcon session
 * flash message adapter.
 *
 * @package   Kolibri\Library
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @since     May 2013
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
class Flash extends \Phalcon\Flash\Session
{

    public function output($remove = null)
    {
        $messages = $this->getMessages(null, true);
        foreach ($messages as $message) {
            echo '<div data-alert class="alert-box secondary">' . $message[0] . '<a href="#" class="close">&times;</a></div>';
        }
    }

}