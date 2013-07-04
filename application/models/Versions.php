<?php

namespace Kolibri\Models;

/**
 * Versions model.
 *
 * This model defines a row in the versions table.
 *
 * @package   Kolibri\Library
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @since     May 2013
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
class Versions extends \Phalcon\Mvc\Model
{

    public $id;
    public $page_id;
    public $version;
    public $content;
    public $created;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo(
            'page_id',
            'Kolibri\Models\Pages',
            'id',
            array(
                'alias' => 'Pages'
            )
        );
    }

    public function getSource()
    {
        return 'versions';
    }

    public function beforeValidationOnCreate()
    {
        $this->created = date('Y-m-d H:i:s');
    }

}