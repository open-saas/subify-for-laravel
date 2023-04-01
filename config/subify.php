<?php

return [
    'persistence' => [
        'eloquent' => [
            'benefit' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\Benefit::class,
                'table' => 'benefits',
            ],

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
