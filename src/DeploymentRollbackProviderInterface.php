<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface DeploymentRollbackProviderInterface
{
    /**
     * Restore a named release, or the latest available release when omitted.
     */
    public function rollback(object $project, object $profile, ?string $release = null): bool;
}
