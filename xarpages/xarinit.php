<?php

/**
 * File: $Id$
 *
 * Initialise the xarpages module.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Jason Judge 
 */

function xarpages_init()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $pagestable = $xartable['xarpages_pages'];
    $typestable = $xartable['xarpages_types'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /*
        CREATE TABLE `xar_xarpages_pages` (
          `xar_pid` int(11) NOT NULL auto_increment,
          `xar_name` varchar(100) NOT NULL default '',
          `xar_desc` text,
          `xar_itemtype` int(11) NOT NULL default '0',
          `xar_parent` int(11) NOT NULL default '0',
          `xar_left` int(11) NOT NULL default '0',
          `xar_right` int(11) NOT NULL default '0',
          `xar_template` varchar(100) default NULL,
          `xar_theme` varchar(100) default NULL,
          `xar_encode_url` varchar(100) default NULL,
          `xar_decode_url` varchar(100) default NULL,
          `xar_function` varchar(100) default NULL,
          `xar_status` varchar(20) NOT NULL default 'ACTIVE',
          `xar_alias` tinyint(4) NOT NULL default '0',
          PRIMARY KEY  (`xar_pid`)
        ) ;
    */

    $fields = "
        xar_pid             I           AUTO    PRIMARY,
        xar_name            C(100)      NotNull DEFAULT '',
        xar_desc            X           Null,
        xar_itemtype        I           NotNull DEFAULT 0,
        xar_parent          I           NotNull DEFAULT 0,
        xar_left            I           NotNull DEFAULT 0,
        xar_right           I           NotNull DEFAULT 0,
        xar_template        C(100)      Null,
        xar_theme           C(100)      Null,
        xar_encode_url      C(100)      Null,
        xar_decode_url      C(100)      Null,
        xar_function        C(100)      Null,
        xar_status          C(20)       NotNull DEFAULT 'ACTIVE',
        xar_alias           L           NotNull DEFAULT 0
    ";

    // Create or alter the table as necessary.
    $result = $datadict->changeTable($pagestable, $fields);    
    if (!$result) {return;}

    // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_xarpages_page_left',
        $pagestable,
        'xar_left'
    );
    if (!$result) {return;}

    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_xarpages_page_name',
        $pagestable,
        'xar_name'
    );
    if (!$result) {return;}

    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_xarpages_page_type',
        $pagestable,
        'xar_itemtype'
    );
    if (!$result) {return;}

    /*
        CREATE TABLE `xar_xarpages_types` (
          `xar_ptid` int(11) NOT NULL auto_increment,
          `xar_name` varchar(100) NOT NULL default '',
          `xar_desc` varchar(200) default NULL,
          PRIMARY KEY  (`xar_ptid`)
        ) ;
    */
    
    $fields = "
        xar_ptid            I           AUTO    PRIMARY,
        xar_name            C(100)      NotNull DEFAULT '',
        xar_desc            C(200)      Null
    ";

    // Create or alter the table as necessary.
    $result = $datadict->changeTable($typestable, $fields);    
    if (!$result) {return;}

    // Set up module variables.
    xarModSetVar('xarpages', 'defaultpage', 0);
    xarModSetVar('xarpages', 'errorpage', 0);
    xarModSetVar('xarpages', 'notfoundpage', 0);
    xarModSetVar('xarpages', 'SupportShortURLs', 0);

    // TODO: Create some default types and DD objects.
    // This would probably best be done via an import after
    // the module is installed.

    // Initialisation successful.
    return true;
}

/**
 * Upgrade the example module from an old version.
 */
function xarpages_upgrade($oldversion)
{
    // Upgrade dependent on old version number.
    switch ($oldversion) {
        case '0.1.0':
        default:
            break;
    }

    // Update successful.
    return true;
}

/**
 * Delete (remove) the module.
 */
function xarpages_delete()
{
    // Deletion successful.
    return true;
}

?>