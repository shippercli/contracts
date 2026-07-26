<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface DeploymentProviderInterface
{
    /**
     * Validate the configuration for this provider.
     *
     * @return array<string> Array of validation errors, empty if valid
     */
    public function validate(object $project, object $profile): array;

    /**
     * Plan the deployment (dry-run).
     *
     * @return array<string, mixed> Plan details
     */
    public function plan(object $project, object $profile): array;

    /**
     * Execute the deployment.
     */
    public function apply(object $project, object $profile): bool;

    /**
     * Destroy the deployment.
     */
    public function destroy(object $project, object $profile): bool;

    /**
     * Get provider name.
     */
    public function getName(): string;

    /**
     * Get the last error message from a failed operation.
     */
    public function getLastError(): string;
}
