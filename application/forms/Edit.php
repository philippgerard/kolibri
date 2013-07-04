<?php

namespace Kolibri\Forms;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;

/**
 * Edit form.
 *
 * This class defines the contents of the form used to edit a page.
 *
 * @package   Kolibri\Forms
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @since     May 2013
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
class Edit extends Form
{
    public function initialize()
    {
        $content = new TextArea('content', array('rows' => 35, 'style' => 'display: none', 'spellcheck' => 'false'));
        $content->addValidator(
            new PresenceOf(array(
                'message' => 'Some content is required'
            ))
        );
        $this->add($content);

        $id = new Hidden('id');
        $id->addValidator(
            new Regex(array(
                'pattern' => '/[0-9]*/',
                'message' => 'The page id can only be numerical'
            ))
        );
        $this->add($id);
    }

}
