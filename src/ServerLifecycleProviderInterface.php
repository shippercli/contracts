<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface ServerLifecycleProviderInterface
{
    /**
     * Resolve an existing provider server.
     *
     * @param array<string, mixed> $server
     * @return array<string, mixed>|null
     */
    public function resolveServer(array $server): ?array;

    /**
     * Provision a server and return its provider resource details.
     *
     * @param array<string, mixed> $spec
     * @return array<string, mixed>
     */
    public function createServer(array $spec): array;

    /**
     * Fetch provider details for a server.
     *
     * @return array<string, mixed>
     */
    public function server(string $serverId): array;

    /**
     * Delete a server previously created by Shipper.
     *
     * Providers must reject this operation when ownership cannot be proven.
     */
    public function deleteServer(string $serverId, string $ownershipToken): bool;
}
