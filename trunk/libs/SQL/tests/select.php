<?php
/**
 * @package SQL_Parser
 * @ignore
 */
$tests = array(
array('sql' => 'SELECT 1a',
'expect' => 'Parse error: Expected columns or a set function on line 1
SELECT 1a
       ^ found: "1"'
),
array('sql' => 'SELECT 1 from dog',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => '1'
            ),
        'tables' => array(
            0 => 'dog'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array('sql' => 'SELECT 1 a from dog',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            'a' => '1'
            ),
        'tables' => array(
            0 => 'dog'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array(
'sql' => 'select * from dog where cat <> 4',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            ),
        'tables' => array(
            0 => 'dog'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'cat',
                'type' => 'ident'
                ),
            'op' => '<>',
            'arg_2' => array(
                'value' => 4,
                'type' => 'int_val'
                )
            )
        )
),
array(
'sql' => 'select legs, hairy from dog',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'legs',
            1 => 'hairy'
            ),
        'tables' => array(
            0 => 'dog'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array(
'sql' => 'select max(length) from dog',
'expect' => array(
        'command' => 'select',
        'set_function' => array(
            0 => array(
                'name' => 'max',
                'arg' => 'length'
                )
            ),
        'tables' => array(
            0 => 'dog'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array(
'sql' => 'select count(distinct country) from publishers',
'expect' => array(
        'command' => 'select',
        'set_function' => array(
            0 => array(
                'name' => 'count',
                'distinct' => true,
                'arg' => array(
                    0 => 'country'
                    )
                )
            ),
        'tables' => array(
            0 => 'publishers'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array(
'sql' => 'select one, two from hairy where two <> 4 and one = 2',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'one',
            1 => 'two'
            ),
        'tables' => array(
            0 => 'hairy'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => 'two',
                    'type' => 'ident'
                    ),
                'op' => '<>',
                'arg_2' => array(
                    'value' => 4,
                    'type' => 'int_val'
                    )
                ),
            'op' => 'and',
            'arg_2' => array(
                'arg_1' => array(
                    'value' => 'one',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => 2,
                    'type' => 'int_val'
                    )
                )
            )
        )
),
array(
'sql' => 'select one, two from hairy where two <> 4 and one = 2 order by two',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'one',
            1 => 'two'
            ),
        'tables' => array(
            0 => 'hairy'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => 'two',
                    'type' => 'ident'
                    ),
                'op' => '<>',
                'arg_2' => array(
                    'value' => 4,
                    'type' => 'int_val'
                    )
                ),
            'op' => 'and',
            'arg_2' => array(
                'arg_1' => array(
                    'value' => 'one',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => 2,
                    'type' => 'int_val'
                    )
                )
            ),
        'sort_order' => array(
            'two' => 'asc'
            )
        )
),
array(
'sql' => 'select one, two from hairy where two <> 4 and one = 2 limit 4 order by two ascending, dog descending',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'one',
            1 => 'two'
            ),
        'tables' => array(
            0 => 'hairy'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => 'two',
                    'type' => 'ident'
                    ),
                'op' => '<>',
                'arg_2' => array(
                    'value' => 4,
                    'type' => 'int_val'
                    )
                ),
            'op' => 'and',
            'arg_2' => array(
                'arg_1' => array(
                    'value' => 'one',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => 2,
                    'type' => 'int_val'
                    )
                )
            ),
        'limit_clause' => array(
            'start' => 0,
            'length' => 4
            ),
        'sort_order' => array(
            'two' => 'asc',
            'dog' => 'desc'
            )
        )
),
array(
'sql' => 'select foo.a from foo',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'foo.a'
            ),
        'tables' => array(
            0 => 'foo'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array(
'sql' => 'select a as b, min(a) as baz from foo',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            'b' => 'a'
            ),
        'set_function' => array(
            'baz' => array(
                'name' => 'min',
                'arg' => 'a'
                )
            ),
        'tables' => array(
            0 => 'foo'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array(
'sql' => 'select a from foo as bar',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'a'
            ),
        'tables' => array(
            'bar' => 'foo'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array(
'sql' => 'select * from person where surname is not null and firstname = \'jason\'',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            ),
        'tables' => array(
            0 => 'person'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => 'surname',
                    'type' => 'ident'
                    ),
                'op' => 'is',
                'neg' => true,
                'arg_2' => array(
                    'value' => '',
                    'type' => 'null'
                    )
                ),
            'op' => 'and',
            'arg_2' => array(
                'arg_1' => array(
                    'value' => 'firstname',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => 'jason',
                    'type' => 'text_val'
                    )
                )
            )
        )
),
array(
'sql' => 'select * from person where surname is null',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            ),
        'tables' => array(
            0 => 'person'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'surname',
                'type' => 'ident'
                ),
            'op' => 'is',
            'arg_2' => array(
                'value' => '',
                'type' => 'null'
                )
            )
        )
),
array(
'sql' => 'select * from person where surname = \'\' and firstname = \'jason\'',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            ),
        'tables' => array(
            0 => 'person'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => 'surname',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => '',
                    'type' => 'text_val'
                    )
                ),
            'op' => 'and',
            'arg_2' => array(
                'arg_1' => array(
                    'value' => 'firstname',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => 'jason',
                    'type' => 'text_val'
                    )
                )
            )
        )
),
array(
'sql' => 'select table_1.id, table_2.name from table_1, table_2 where table_2.table_1_id = table_1.id',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'table_1.id',
            1 => 'table_2.name'
            ),
        'tables' => array(
            0 => 'table_1',
            1 => 'table_2'
            ),
        'table_join_clause' => array(
            0 => '',
            1 => ''
            ),
        'table_join' => array(
            0 => ','
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'table_2.table_1_id',
                'type' => 'ident'
                ),
            'op' => '=',
            'arg_2' => array(
                'value' => 'table_1.id',
                'type' => 'ident'
                )
            )
        )
),
array(
'sql' => 'select a from table_1 where a not in (select b from table_2)',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'a'
            ),
        'tables' => array(
            0 => 'table_1'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'a',
                'type' => 'ident'
                ),
            'op' => 'in',
            'neg' => true,
            'arg_2' => array(
                'value' => array(
                    'command' => 'select',
                    'columns' => array(
                        0 => 'b'
                        ),
                    'tables' => array(
                        0 => 'table_2'
                        ),
                    'table_join_clause' => array(
                        0 => ''
                        )
                    ),
                'type' => 'command'
                )
            )
        )
),
array(
'sql' => 'select a from table_1 where a in (select b from table_2 where c not in (select d from table_3))',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'a'
            ),
        'tables' => array(
            0 => 'table_1'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'a',
                'type' => 'ident'
                ),
            'op' => 'in',
            'arg_2' => array(
                'value' => array(
                    'command' => 'select',
                    'columns' => array(
                        0 => 'b'
                        ),
                    'tables' => array(
                        0 => 'table_2'
                        ),
                    'table_join_clause' => array(
                        0 => ''
                        ),
                    'where_clause' => array(
                        'arg_1' => array(
                            'value' => 'c',
                            'type' => 'ident'
                            ),
                        'op' => 'in',
                        'neg' => true,
                        'arg_2' => array(
                            'value' => array(
                                'command' => 'select',
                                'columns' => array(
                                    0 => 'd'
                                    ),
                                'tables' => array(
                                    0 => 'table_3'
                                    ),
                                'table_join_clause' => array(
                                    0 => ''
                                    )
                                ),
                            'type' => 'command'
                            )
                        )
                    ),
                'type' => 'command'
                )
            )
        )
),
array(
'sql' => 'select a from table_1 where a in (1, 2, 3)',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'a'
            ),
        'tables' => array(
            0 => 'table_1'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'a',
                'type' => 'ident'
                ),
            'op' => 'in',
            'arg_2' => array(
                'value' => array(
                    0 => 1,
                    1 => 2,
                    2 => 3
                    ),
                'type' => array(
                    0 => 'int_val',
                    1 => 'int_val',
                    2 => 'int_val'
                    )
                )
            )
        )
),
array(
'sql' => 'select count(child_table.name) from parent_table ,child_table where parent_table.id = child_table.id',
'expect' => array(
        'command' => 'select',
        'set_function' => array(
            0 => array(
                'name' => 'count',
                'arg' => array(
                    0 => 'child_table.name'
                    )
                )
            ),
        'tables' => array(
            0 => 'parent_table',
            1 => 'child_table'
            ),
        'table_join_clause' => array(
            0 => '',
            1 => ''
            ),
        'table_join' => array(
            0 => ','
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'parent_table.id',
                'type' => 'ident'
                ),
            'op' => '=',
            'arg_2' => array(
                'value' => 'child_table.id',
                'type' => 'ident'
                )
            )
        )
),
array(
'sql' => 'select parent_table.name, count(child_table.name) from parent_table ,child_table where parent_table.id = child_table.id group by parent_table.name',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'parent_table.name'
            ),
        'set_function' => array(
            0 => array(
                'name' => 'count',
                'arg' => array(
                    0 => 'child_table.name'
                    )
                )
            ),
        'tables' => array(
            0 => 'parent_table',
            1 => 'child_table'
            ),
        'table_join_clause' => array(
            0 => '',
            1 => ''
            ),
        'table_join' => array(
            0 => ','
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'parent_table.id',
                'type' => 'ident'
                ),
            'op' => '=',
            'arg_2' => array(
                'value' => 'child_table.id',
                'type' => 'ident'
                )
            ),
        'group_by' => array(
            0 => 'parent_table.name'
            )
        )
),
array(
'sql' => 'select * from cats where furry = 1 group by name, type',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            ),
        'tables' => array(
            0 => 'cats'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'value' => 'furry',
                'type' => 'ident'
                ),
            'op' => '=',
            'arg_2' => array(
                'value' => 1,
                'type' => 'int_val'
                )
            ),
        'group_by' => array(
            0 => 'name',
            1 => 'type'
            )
        )
),
array(
'sql' => 'select a, max(b) as x, sum(c) as y, min(d) as z from e',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'a'
            ),
        'set_function' => array(
            'x' => array(
                'name' => 'max',
                'arg' => 'b'
                ),
            'y' => array(
                'name' => 'sum',
                'arg' => 'c'
                ),
            'z' => array(
                'name' => 'min',
                'arg' => 'd'
                )
            ),
        'tables' => array(
            0 => 'e'
            ),
        'table_join_clause' => array(
            0 => ''
            )
        )
),
array(
'sql' => 'select clients_translation.id_clients_prefix, clients_translation.rule_number,
       clients_translation.pattern, clients_translation.rule
       from clients, clients_prefix, clients_translation
       where (clients.id_softswitch = 5)
         and (clients.id_clients = clients_prefix.id_clients)
         and clients.enable=\'y\'
         and clients.unused=\'n\'
         and (clients_translation.id_clients_prefix = clients_prefix.id_clients_prefix)
         order by clients_translation.id_clients_prefix,clients_translation.rule_number',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'clients_translation.id_clients_prefix',
            1 => 'clients_translation.rule_number',
            2 => 'clients_translation.pattern',
            3 => 'clients_translation.rule'
            ),
        'tables' => array(
            0 => 'clients',
            1 => 'clients_prefix',
            2 => 'clients_translation'
            ),
        'table_join_clause' => array(
            0 => '',
            1 => '',
            2 => ''
            ),
        'table_join' => array(
            0 => ',',
            1 => ','
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => array(
                        'arg_1' => array(
                            'value' => 'clients.id_softswitch',
                            'type' => 'ident'
                            ),
                        'op' => '=',
                        'arg_2' => array(
                            'value' => 5,
                            'type' => 'int_val'
                            )
                        ),
                    'type' => 'subclause'
                    )
                ),
            'op' => 'and',
            'arg_2' => array(
                'arg_1' => array(
                    'arg_1' => array(
                        'value' => array(
                            'arg_1' => array(
                                'value' => 'clients.id_clients',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => 'clients_prefix.id_clients',
                                'type' => 'ident'
                                )
                            ),
                        'type' => 'subclause'
                        )
                    ),
                'op' => 'and',
                'arg_2' => array(
                    'arg_1' => array(
                        'arg_1' => array(
                            'value' => 'clients.enable',
                            'type' => 'ident'
                            ),
                        'op' => '=',
                        'arg_2' => array(
                            'value' => 'y',
                            'type' => 'text_val'
                            )
                        ),
                    'op' => 'and',
                    'arg_2' => array(
                        'arg_1' => array(
                            'arg_1' => array(
                                'value' => 'clients.unused',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => 'n',
                                'type' => 'text_val'
                                )
                            ),
                        'op' => 'and',
                        'arg_2' => array(
                            'arg_1' => array(
                                'value' => array(
                                    'arg_1' => array(
                                        'value' => 'clients_translation.id_clients_prefix',
                                        'type' => 'ident'
                                        ),
                                    'op' => '=',
                                    'arg_2' => array(
                                        'value' => 'clients_prefix.id_clients_prefix',
                                        'type' => 'ident'
                                        )
                                    ),
                                'type' => 'subclause'
                                )
                            )
                        )
                    )
                )
            ),
        'sort_order' => array(
            'clients_translation.id_clients_prefix' => 'asc',
            'clients_translation.rule_number' => 'asc'
            )
        )
),
array(
'sql' => 'SELECT column1,column2
FROM table1
WHERE (column1=\'1\' AND column2=\'1\') OR (column3=\'1\' AND column4=\'1\')',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'column1',
            1 => 'column2'
            ),
        'tables' => array(
            0 => 'table1'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => array(
                        'arg_1' => array(
                            'arg_1' => array(
                                'value' => 'column1',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => '1',
                                'type' => 'text_val'
                                )
                            ),
                        'op' => 'and',
                        'arg_2' => array(
                            'arg_1' => array(
                                'value' => 'column2',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => '1',
                                'type' => 'text_val'
                                )
                            )
                        ),
                    'type' => 'subclause'
                    )
                ),
            'op' => 'or',
            'arg_2' => array(
                'arg_1' => array(
                    'value' => array(
                        'arg_1' => array(
                            'arg_1' => array(
                                'value' => 'column3',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => '1',
                                'type' => 'text_val'
                                )
                            ),
                        'op' => 'and',
                        'arg_2' => array(
                            'arg_1' => array(
                                'value' => 'column4',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => '1',
                                'type' => 'text_val'
                                )
                            )
                        ),
                    'type' => 'subclause'
                    )
                )
            )
        )
),
array(
'sql' => '-- Test Comment',
'expect' => 'Parse error: Nothing to do on line 1
-- Test Comment
                ^ found: "*end of input*"'

),
array(
'sql' => '# Test Comment',
'expect' => 'Parse error: Nothing to do on line 1
# Test Comment
               ^ found: "*end of input*"'

),
array(
'sql' => 'SELECT name FROM people WHERE id > 1 AND (name = \'arjan\' OR name = \'john\')',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'name'
            ),
        'tables' => array(
            0 => 'people'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => 'id',
                    'type' => 'ident'
                    ),
                'op' => '>',
                'arg_2' => array(
                    'value' => 1,
                    'type' => 'int_val'
                    )
                ),
            'op' => 'and',
            'arg_2' => array(
                'arg_1' => array(
                    'value' => array(
                        'arg_1' => array(
                            'arg_1' => array(
                                'value' => 'name',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => 'arjan',
                                'type' => 'text_val'
                                )
                            ),
                        'op' => 'or',
                        'arg_2' => array(
                            'arg_1' => array(
                                'value' => 'name',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => 'john',
                                'type' => 'text_val'
                                )
                            )
                        ),
                    'type' => 'subclause'
                    )
                )
            )
        )
),
array(
'sql' => 'select * from test where (field1 = \'x\' and field2 <>\'y\') or field3 = \'z\'',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            ),
        'tables' => array(
            0 => 'test'
            ),
        'table_join_clause' => array(
            0 => ''
            ),
        'where_clause' => array(
            'arg_1' => array(
                'arg_1' => array(
                    'value' => array(
                        'arg_1' => array(
                            'arg_1' => array(
                                'value' => 'field1',
                                'type' => 'ident'
                                ),
                            'op' => '=',
                            'arg_2' => array(
                                'value' => 'x',
                                'type' => 'text_val'
                                )
                            ),
                        'op' => 'and',
                        'arg_2' => array(
                            'arg_1' => array(
                                'value' => 'field2',
                                'type' => 'ident'
                                ),
                            'op' => '<>',
                            'arg_2' => array(
                                'value' => 'y',
                                'type' => 'text_val'
                                )
                            )
                        ),
                    'type' => 'subclause'
                    )
                ),
            'op' => 'or',
            'arg_2' => array(
                'arg_1' => array(
                    'value' => 'field3',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => 'z',
                    'type' => 'text_val'
                    )
                )
            )
        )
),
array(
'sql' => 'select a, d from b inner join c on b.a = c.a',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'a',
            1 => 'd'
            ),
        'tables' => array(
            0 => 'b',
            1 => 'c'
            ),
        'table_join_clause' => array(
            0 => '',
            1 => array(
                'arg_1' => array(
                    'value' => 'b.a',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => 'c.a',
                    'type' => 'ident'
                    )
                )
            ),
        'table_join' => array(
            0 => 'inner join'
            )
        )
),
array(
'sql' => 'select a, d from b inner join c on b.a = c.a left outer join q on r < m',
'expect' => array(
        'command' => 'select',
        'columns' => array(
            0 => 'a',
            1 => 'd'
            ),
        'tables' => array(
            0 => 'b',
            1 => 'c',
            2 => 'q'
            ),
        'table_join_clause' => array(
            0 => '',
            1 => array(
                'arg_1' => array(
                    'value' => 'b.a',
                    'type' => 'ident'
                    ),
                'op' => '=',
                'arg_2' => array(
                    'value' => 'c.a',
                    'type' => 'ident'
                    )
                ),
            2 => array(
                'arg_1' => array(
                    'value' => 'r',
                    'type' => 'ident'
                    ),
                'op' => '<',
                'arg_2' => array(
                    'value' => 'm',
                    'type' => 'ident'
                    )
                )
            ),
        'table_join' => array(
            0 => 'inner join',
            1 => 'left outer join'
            )
        )
)
);
?>
