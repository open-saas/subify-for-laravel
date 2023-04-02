<?php

return [
    'persistence' => [
        'eloquent' => [
            'benefit' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\Benefit::class,
                'table' => 'benefits',
            ],

            'benefit_usage' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\BenefitUsage::class,
                'table' => 'benefit_usages',
            ],

            'plan' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\Plan::class,
                'table' => 'plans',
            ],

            'plan_regime' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\PlanRegime::class,
                'table' => 'plan_regimes',
            ],

            'benefit_plan' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\BenefitPlan::class,
                'table' => 'benefit_plan',
            ],

            'subscription' => [
                'model' => \OpenSaaS\Subify\Persistence\Models\Subscription::class,
                'table' => 'subscriptions',
            ],
        ],
    ],
];
