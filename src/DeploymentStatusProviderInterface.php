<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface DeploymentStatusProviderInterface
{
    /**
     * Return the current deployment and provider resource state.
     *
     * @return array<string, mixed>
     */
    public function status(object $project, object $profile): array;
}
