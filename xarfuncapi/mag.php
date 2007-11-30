<?php

/**
 * Display a magazine from the mag module within a html xarpage.
 *
 * Note: one thing to watch out for: ensure any module stylesheets called up
 * within the mag module have the module="mag" attribute set, otherwise the
 * core will be looking for the stylesheets in the xarpages module. The same
 * goes for included templates.
 *
 * @todo Make this more generic, so a xarpages page type can call up the main page
 * for any generic module, using standard rules. The rules need standardising, naturally.
 *
 */

function xarpages_funcapi_mag($args)
{
    // Allow the magazine reference to be locked in by creating a 'mid'
    // (magazine ID) DD property.
    if (!empty($args['dd']['mid'])) {
        $mid = $args['dd']['mid'];
    } else {
        $mid = 0;
    }

    // Fall-back message, in case the default html template is used by accident.
    $args['dd']['body'] = xarML('Use the "html-external" page type with this function.');

    // Call up the main mag module function.
    // Check the module is installed first.
    if (xarModIsAvailable('mag')) {
        $args['content'] = xarModfunc('mag', 'user', 'main', array('pid' => $args['pid'], 'mid' => $mid));
    } else {
        $args['content'] = xarML('Module "mag" is not installed or available.');
    }

    return $args;
}

?>