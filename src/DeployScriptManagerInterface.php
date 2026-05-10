<?php

declare(strict_types=1);

namespace ShipperCliContracts;

use ShipperCliContractsTypesProfileConfig;
use ShipperCliContractsTypesProjectConfig;

interface DeployScriptManagerInterface
{
    /**
     * Plan deploy script configuration.
     *
     * @return array<string>
     */
    public function plan(ProjectConfig $project, ProfileConfig $profile): array;

    /**
     * Apply deploy script configuration.
     *
     * @return array<string, mixed>
     */
    public function apply(int $serverId, int $siteId, string $script): array;
}
