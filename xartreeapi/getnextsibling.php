<?php

/*
 * Get the parent/left/right values for a single item.
 * Will include the virtual item '0' if necessary.
 * id: ID of the item.
 * tablename: name of table
 * idname: name of the ID column
 */

function xarpages_treeapi_getnextsibling($args)
{
    // Expand the arguments.
    extract($args);

    // Database.
    $dbconn =& xarDB::getConn();

    if ($id <> 0) {
        // Insert point is a real item.
        $query = "SELECT 
                    parent.$idname
                  FROM 
                    $tablename AS node, 
                    $tablename AS parent 
                  WHERE 
                    parent.xar_left = node.xar_right + 1
                    AND 
                    node.$idname = ?";
        // return result
        if (!$result->EOF) {
            [$pid] = $result->fields;
        }
        if (isset($pid)) {
            return $pid;
        } else {
            return;
        }
    } else {
        // Insert point is the virtual root.
        // Virtual root has no siblings
        return;
    }
}
