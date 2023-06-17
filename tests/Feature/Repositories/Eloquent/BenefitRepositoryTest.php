<?php

namespace Tests\Feature\Repositories\Eloquent;

use OpenSaaS\Subify\Repositories\Eloquent\BenefitRepository;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Benefit;
use Tests\Feature\TestCase;

/**
 * @internal
 */
class BenefitRepositoryTest extends TestCase
{
    private BenefitRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(BenefitRepository::class);
    }

    public function testAllReturnBenefits(): void
    {
        $benefit = Benefit::factory()
            ->create();

        $benefits = $this->repository->all();

        $this->assertEquals([$benefit->toEntity()], $benefits);
    }
}
