<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface AliasManagerInterface
{
    /**
     * Plan alias configuration.
     *
     * @return array<string>
     */
    public function plan(object $project, object $profile): array;

    /**
     * Apply alias configuration.
     *
     * @param array<int, string> $aliases
     *
     * @return array<string, mixed>
     */
    public function apply(int $serverId, int $siteId, array $aliases): array;
}
