<?php

namespace Kolibri\Plugins;

/**
 * Class TidyPlugin.
 *
 * Convenience plugin that is triggered by the view:afterRender-event and tidies up the HTML
 * according to HTML5 rules using php-tidy. Should not be in use in production, but makes
 * the source code a lot nicer to read while developing.
 *
 * Unfortunately breaks smart editors because it brutally adds whitespace where no whitespace
 * belongs. So only use while testing.
 *
 * @author    Philipp Gérard <philipp.gerard@zeitdenken.de>
 *
 * @since     May 2013
 *
 * @copyright Philipp Gérard <philipp.gerard@zeitdenken.de>
 * @license   MIT License http://opensource.org/licenses/MIT
 */
class Tidy
{
    public function afterRender($event, $view)
    {
        if (!extension_loaded('tidy')) {
            return;
        }
        $options = [
            'hide-comments'       => true,
            'tidy-mark'           => false,
            'indent'              => true,
            'indent-spaces'       => 4,
            'new-blocklevel-tags' => 'article,header,footer,section,nav',
            'new-inline-tags'     => 'video,audio,canvas,ruby,rt,rp',
            'doctype'             => '<!DOCTYPE HTML>',
            'sort-attributes'     => 'alpha',
            'vertical-space'      => false,
            'output-xhtml'        => true,
            'wrap'                => 150,
            'wrap-attributes'     => false,
            'break-before-br'     => false,
        ];

        $buffer = tidy_parse_string($view->getContent(), $options, 'utf8');
        tidy_clean_repair($buffer);
        $buffer = str_replace(
            [
                '<html lang="en" xmlns="http://www.w3.org/1999/xhtml">',
                '<html xmlns="http://www.w3.org/1999/xhtml">',
            ],
            '<!DOCTYPE html>',
            $buffer
        );
        $buffer = str_replace(">\n</script>", '></script>', $buffer);
        $view->setContent((string) $buffer);
    }
}
