<?php
/**
 * @package SQL_Parser
 * @ignore
 */
$tests = array(
array(
'sql' => 'drop table dishes cascade',
'expect' => array(
        'command' => 'drop_table',
        'target' => array(
            0 => 'dishes'
            ),
        'drop_behavior' => 'cascade'
        )
),
array(
'sql' => 'drop table bondage restrict',
'expect' => array(
        'command' => 'drop_table',
        'target' => array(
            0 => 'bondage'
            ),
        'drop_behavior' => 'restrict'
        )
),
array(
'sql' => 'drop index i',
'expect' => array(
        'command' => 'drop_index',
        'target' => array(
            0 => 'i'
            )
        )
),
array(
'sql' => 'drop constraint c',
'expect' => array(
        'command' => 'drop_constraint',
        'target' => array(
            0 => 'c'
            )
        )
),
array(
'sql' => 'drop sequence s ;',
'expect' => array(
        'command' => 'drop_sequence',
        'target' => array(
            0 => 's'
            )
        )
),
array(
'sql' => 'drop table play cascade restrict',
'expect' => 'Parse error: Unexpected token on line 1
drop table play cascade restrict
                        ^ found: "restrict"'

),
array(
'sql' => 'drop table cat where mouse = floor',
'expect' => 'Parse error: Unexpected token on line 1
drop table cat where mouse = floor
                     ^ found: "mouse"'

),
array(
'sql' => 'drop elephant',
'expect' => 'Parse error: Unknown object to drop on line 1
drop elephant
     ^ found: "elephant"'

),
);
?>