<?php

return [
    'repositories' => [
        'cache' => [
            'enabled' => false,
            'prefix' => '__subify:',
            'store' => null, // set it to `null` to use the default cache store
            'ttl' => DateInterval::createFromDateString('1 day'),
        ],

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
