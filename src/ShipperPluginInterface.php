<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface ShipperPluginInterface
{
    /**
     * Get the plugin's service providers.
     *
     * @return array<class-string, class-string>
     */
    public function providers(): array;
}