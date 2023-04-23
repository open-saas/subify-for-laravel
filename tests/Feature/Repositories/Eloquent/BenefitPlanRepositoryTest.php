<?php

namespace Repositories\Eloquent;

use OpenSaaS\Subify\Repositories\Eloquent\BenefitPlanRepository;
use OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitPlan;
use Tests\Feature\TestCase;

/**
 * @internal
 */
class BenefitPlanRepositoryTest extends TestCase
{
    private BenefitPlanRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(BenefitPlanRepository::class);
    }

    public function testAllReturnBenefitPlans(): void
    {
        $benefitPlan = BenefitPlan::factory()
            ->create();

        $benefitPlans = $this->repository->all();

        $this->assertEquals([$benefitPlan->toEntity()], $benefitPlans);
    }
}
