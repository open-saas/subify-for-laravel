<?php

namespace OpenSaaS\Subify\Repositories\Cache\Concerns;

use Illuminate\Contracts\Config\Repository as ConfigRepository;

trait HandlesPrefix
{
    protected ConfigRepository $configRepository;
    private string $prefix;

    protected function prefixed(string $key): string
    {
        if (!isset($this->prefix)) {
            $this->prefix = $this->configRepository->get('subify.repositories.cache.prefix');
        }

        return $this->prefix.$key;
    }
}
