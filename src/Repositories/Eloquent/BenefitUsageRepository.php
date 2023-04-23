<?php

namespace OpenSaaS\Subify\Repositories\Eloquent;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use OpenSaaS\Subify\Contracts\Database\BenefitUsageRepository as DatabaseBenefitUsageRepository;
use OpenSaaS\Subify\Entities\BenefitUsage as BenefitUsageEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Concerns\QueriesBySubscriberIdentifier;
use OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitUsage;

class BenefitUsageRepository implements DatabaseBenefitUsageRepository
{
    use QueriesBySubscriberIdentifier;

    private BenefitUsage $model;

    public function __construct(
        private Container $container,
        private ConfigRepository $configRepository,
    ) {
        $modelClass = $this->configRepository->get('subify.repositories.eloquent.benefit_usage.model');
        $this->model = $this->container->make($modelClass);
    }

    /**
     * @return BenefitUsageEntity[]
     */
    public function get(string $subscriberIdentifier): array
    {
        /** @var BenefitUsage[] $benefits */
        $benefits = $this->model
            ->newQuery()
            ->where($this->subscriberIs($subscriberIdentifier))
            ->get()
            ->all();

        return array_map(fn (BenefitUsage $benefit) => $benefit->toEntity(), $benefits);
    }

    public function insert(string $subscriberIdentifier, int $benefitId, float $amount, ?\DateTimeInterface $expiration): BenefitUsageEntity
    {
        $benefitUsage = $this->model->newInstance([
            'benefit_id' => $benefitId,
            'amount' => $amount,
            'expired_at' => $expiration,
        ]);

        $benefitUsage->setSubscriberIdentifier($subscriberIdentifier);
        $benefitUsage->save();

        return $benefitUsage->toEntity();
    }

    public function save(BenefitUsageEntity $benefitUsage): void
    {
        $this->model
            ->newQuery()
            ->where('id', '=', $benefitUsage->getId())
            ->update([
                'amount' => $benefitUsage->getAmount(),
                'expired_at' => $benefitUsage->getExpiredAt(),
            ]);
    }
}
