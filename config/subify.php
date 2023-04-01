<?php

return [
    'persistence' => [
        'eloquent' => [
            'plan' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\Plan::class,
                'table' => 'plans',
            ],

            'plan_regime' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\PlanRegime::class,
                'table' => 'plan_regimes',
            ],
        ],
    ],
];
