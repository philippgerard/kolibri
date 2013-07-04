<?php

namespace Kolibri\Models;

/**
 * Pages model
 *
 * This model defines a row in the pages table.
 *
 * @package   Kolibri\Models
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @since     May 2013
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
class Pages extends \Phalcon\Mvc\Model
{

    public $id;
    public $title;
    public $content;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->hasMany(
            'id',
            'Kolibri\Models\Versions',
            'page_id',
            array(
                'alias' => 'Versions'
            )
        );
    }

    public function getSource()
    {
        return 'pages';
    }

}