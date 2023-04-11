<?php

return (new \PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
    ]);
