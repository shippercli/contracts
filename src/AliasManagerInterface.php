<?php

declare(strict_types=1);

namespace ShipperCliContracts;

use ShipperCliContractsTypesProfileConfig;
use ShipperCliContractsTypesProjectConfig;

interface AliasManagerInterface
{
    /**
     * Plan alias configuration.
     *
     * @return array<string>
     */
    public function plan(ProjectConfig $project, ProfileConfig $profile): array;

    /**
     * Apply alias configuration.
     *
     * @param array<int, string> $aliases
     *
     * @return array<string, mixed>
     */
    public function apply(int $serverId, int $siteId, array $aliases): array;
}
