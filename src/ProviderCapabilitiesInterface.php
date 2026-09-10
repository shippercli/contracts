<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

interface ProviderCapabilitiesInterface
{
    /**
     * Return the provider capability manifest.
     *
     * The returned array must validate with CapabilityManifest::from().
     * Partial capabilities require non-empty notes or limitations.
     *
     * @return array<string, array{state: 'supported'|'partial'|'unsupported', notes?: string, requirements?: list<string>, limitations?: list<string>}>
     */
    public function capabilities(): array;
}
