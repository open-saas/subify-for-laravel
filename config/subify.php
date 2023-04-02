<?php

return [
    'persistence' => [
        'eloquent' => [
            'benefit' => [
                'model' => \OpenSaaS\Subify\Repositories\Eloquent\Models\Benefit::class,
                'table' => 'benefits',
            ],

            'benefit_usage' => [
                'model' => \OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitUsage::class,
                'table' => 'benefit_usages',
            ],

            'plan' => [
                'model' => \OpenSaaS\Subify\Repositories\Eloquent\Models\Plan::class,
                'table' => 'plans',
            ],

            'plan_regime' => [
                'model' => \OpenSaaS\Subify\Repositories\Eloquent\Models\PlanRegime::class,
                'table' => 'plan_regimes',
            ],

            'benefit_plan' => [
                'model' => \OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitPlan::class,
                'table' => 'benefit_plan',
            ],

            'subscription' => [
                'model' => \OpenSaaS\Subify\Repositories\Eloquent\Models\Subscription::class,
                'table' => 'subscriptions',
            ],
        ],
    ],
];
