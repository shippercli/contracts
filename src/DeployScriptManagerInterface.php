<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface DeployScriptManagerInterface
{
    /**
     * Plan deploy script configuration.
     *
     * @return array<string>
     */
    public function plan(object $project, object $profile): array;

    /**
     * Apply deploy script configuration.
     *
     * @return array<string, mixed>
     */
    public function apply(int $serverId, int $siteId, string $script): array;
}
