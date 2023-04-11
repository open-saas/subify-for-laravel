<?php

return (new \PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'php_unit_test_class_requires_covers' => false,
    ]);
