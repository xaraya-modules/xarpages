<?php
/**
 * File: $Id$
 *
 * Displays a menu block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Xarpages Module
 * @author Jason Judge
*/

/**
 * init func
 */

function xarpages_menublock_init()
{
    return array(
        'multi_homed' => true,
        'current_source' => 'AUTO', // Other values: 'DEFAULT'
        'default_pid' => 0, // 0 == 'None'
        'root_pids' => array(),
        'max_level' => 0
    );
}

/**
 * Block info array
 */

function xarpages_menublock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Xarpages Menu Block',
        'module' => 'xarpages',
        'func_update' => 'xarpages_menublock_update',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => 'no notes'
    );
}

/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 * @todo Option to display the menu even when not on a relevant page
 */

function xarpages_menublock_display($blockinfo)
{
    // Security Check
    // TODO: remove this check once it goes into the blocks centrally.
    //if (!xarSecurityCheck('ViewBlocks', 0, 'Block', 'xarpages:menu:' . $blockinfo['name'])) {return;}

    // TODO:
    // We want a few facilities:
    // 1. Set a root higher than the real tree root. Pages will only
    //    be displayed once that root is reached. Effectively set one
    //    or more trees, at any depth, that this menu will cover.
    // 2. Set a 'max depth' value, so only a preset max number of levels
    //    are rendered in a tree.
    // [1 and 2 are a kind of "view window" for levels]
    // 3. Set behaviour when no current page in the xarpages module is
    //    displayed, e.g. hide menu, show default tree or page etc.

    // Get variables from content block.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Pointer to simplify referencing.
    $vars =& $blockinfo['content'];

    if (!empty($vars['root_pids']) && is_array($vars['root_pids'])) {
        $root_pids = $vars['root_pids'];
    } else {
        $root_pids = array();
    }

    // To start with, we need to know the current page.
    // It could be set (fixed) for the block, passed in
    // via the page cache, or simply not present.
    $pid = 0;
    if (empty($vars['current_source']) || $vars['current_source'] == 'AUTO') {
        // Automatic: that means look at the page cache.
        if (xarVarIsCached('Blocks.xarpages', 'current_pid')) {
            $cached_pid = xarVarGetCached('Blocks.xarpages', 'current_pid');
            // Make sure it is numeric.
            if (isset($cached_pid) && is_numeric($cached_pid)) {
                $pid = $cached_pid;
            }
        }
    }

    // Now we may or may not have a page ID.
    // If the page is not set, then check for a default.
    if (empty($pid) && !empty($vars['default_pid'])) {
        // Set the current page to be the default.
        $pid = $vars['default_pid'];
    }

    // The page details *may* have been cached, if
    // we are in the xarpages module, or have several
    // blocks on the same page showing the same tree.
    if (xarVarIsCached('Blocks.xarpages', 'pagedata')) {
        // Pages are cached?
        $pagedata = xarVarGetCached('Blocks.xarpages', 'pagedata');

        // If the cached tree does not contain the current page,
        // then we cannot use it.
        if (!isset($pagedata['pages'][$pid])) {
            $pagedata = array();
        }
    }

    // If there is no pid, then we have no page or tree to display.
    if (empty($pid)) {return;}
    
    // If necessary, check whether the current page is under one of the
    // of the allowed root pids.
    if (!empty($root_pids)) {
        if (!xarModAPIfunc('xarpages', 'user', 'pageintrees', array('pid' => $pid, 'tree_roots' => $root_pids))) {
            return;
        }
    }

    // If we don't have any page data, then fetch it now.
    if (empty($pagedata)) {
        // Get the page data here now.
        $pagedata = xarModAPIfunc(
            'xarpages', 'user', 'getpagestree',
            array(
                'tree_contains_pid' => $pid,
                'dd_flag' => true,
                'key' => 'pid',
                'status' => 'ACTIVE,EMPTY'
            )
        );

        // If $pagedata is empty, then we have an invalid ID or
        // no permissions. Return NULL if so, suppressing the block.
        if (empty($pagedata)) {return;}

        // Cache the data now we have gone to the trouble of fetching the tree.
        // Only cache it if the cache is empty to start with. We only cache a complete
        // tree here, so if any other blocks need it, it contains all possible
        // pages we could need in that tree.
        if (!xarVarIsCached('Blocks.xarpages', 'pagedata')) {
            xarVarSetCached('Blocks.xarpages', 'pagedata', $pagedata);
        }
    }

    // TODO: handle privileges for pages somewhere. The user/display
    // function handles it for the current page, but there is no
    // point the block providing links to pages that cannot be
    // accessed.
    
    // Here we add the various flags to the pagedata, based on
    // the current page.
    $pagedata = xarModAPIfunc(
        'xarpages', 'user', 'addcurrentpageflags',
        array('pagedata' => $pagedata, 'pid' => $pid, 'root_pids' => $root_pids)
    );

    // If not multi-homed, then create a 'root root' page - a virtual page
    // one step back from the displayed root page. This makes the template
    // much easier to implement. The templates need never display the
    // root page passed into them, and always start with the children of
    // that root page.
    if (empty($vars['multi_homed'])) {
        $pagedata['pages'][0] = array(
            'child_keys' => array($pagedata['root_page']['key']),
            'has_children' => true, 'is_ancestor' => true
        );
        unset($pagedata['root_page']);
        $pagedata['root_page'] =& $pagedata['pages'][0];
    }

    // Pass the page data into the block.
    // Merge it in with the existing block details.
    // TODO: It may be quicker to do the merge the other way around?
    $vars = array_merge($vars, $pagedata);

    return $blockinfo;
}

?>