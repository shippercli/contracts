<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface ProviderCapabilitiesInterface
{
    /**
     * Return the provider capability manifest.
     *
     * Each capability must contain a `state` key with one of
     * `supported`, `partial`, or `unsupported`.
     *
     * @return array<string, array{state: string, notes?: string, requirements?: array<int, string>, limitations?: array<int, string>}>
     */
    public function capabilities(): array;
}
