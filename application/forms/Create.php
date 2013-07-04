<?php

namespace Kolibri\Forms;

use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;

/**
 * Create form.
 *
 * This form defines the contents of the form used to add new pages.
 *
 * @package   Kolibri\Forms
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @since     May 2013
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
class Create extends Form
{
    public function initialize()
    {
        $content = new TextArea('content', array(
            'rows'        => 35,
            'style'       => 'display: none;',
            'placeholder' => 'Some Markdown text'
        ));
        $content->addValidator(
            new PresenceOf(array(
                'message' => 'Some content is required'
            ))
        );
        $this->add($content);

        $title = new Text('title', array('placeholder' => 'Title (CamelCase only)', 'autofocus' => true));
        $title->addValidator(
            new PresenceOf(array(
                'message' => 'A title is required'
            ))
        );
        $title->addValidator(
            new Regex(array(
                'pattern' => '/[A-Z][a-zA-Z]*/',
                'message' => 'Only CamelCase or single word titles are allowed'
            ))
        );
        $this->add($title);
    }

}
