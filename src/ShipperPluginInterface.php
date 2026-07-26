<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface ShipperPluginInterface
{
    /**
     * Map provider names to their implementation classes.
     *
     * @return array<string, class-string<DeploymentProviderInterface>>
     */
    public function providers(): array;
}
