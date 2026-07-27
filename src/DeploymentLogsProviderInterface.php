<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface DeploymentLogsProviderInterface
{
    /**
     * Return recent deployment or application log lines.
     *
     * @return array<int, string>
     */
    public function logs(object $project, object $profile, int $lines = 100): array;
}
