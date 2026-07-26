<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface SslManagerInterface
{
    /**
     * Plan SSL certificate configuration.
     *
     * @return array<string>
     */
    public function plan(string $domain, object $ssl): array;

    /**
     * Apply SSL certificate configuration.
     *
     * @return array<string, mixed>
     */
    public function apply(int $serverId, int $siteId, string $domain, object $ssl): array;
}
