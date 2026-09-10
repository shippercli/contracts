<?php

declare(strict_types=1);

use ShipperCli\Contracts\CapabilityManifest;

function validCapabilityManifest(): array
{
    return \array_fill_keys(CapabilityManifest::CAPABILITIES, ['state' => 'supported']);
}

test('accepts the complete canonical capability manifest', function (): void {
    $manifest = validCapabilityManifest();
    $manifest['ssl'] = [
        'state' => 'partial',
        'limitations' => ['Wildcard certificates require manual DNS validation.'],
    ];

    expect(CapabilityManifest::from($manifest)->toArray())->toBe($manifest);
});

test('rejects a manifest with a missing capability', function (): void {
    $manifest = validCapabilityManifest();
    unset($manifest['rollback']);

    expect(fn () => CapabilityManifest::from($manifest))
        ->toThrow(InvalidArgumentException::class, 'Missing capabilities: rollback');
});

test('rejects a manifest with an unknown capability', function (): void {
    $manifest = validCapabilityManifest();
    $manifest['unknown'] = ['state' => 'supported'];

    expect(fn () => CapabilityManifest::from($manifest))
        ->toThrow(InvalidArgumentException::class, 'Unknown capabilities: unknown');
});

test('rejects an invalid capability state', function (): void {
    $manifest = validCapabilityManifest();
    $manifest['ssl'] = ['state' => 'available'];

    expect(fn () => CapabilityManifest::from($manifest))
        ->toThrow(InvalidArgumentException::class, 'Capability ssl has an invalid state');
});

test('rejects partial capabilities without an explanation', function (): void {
    $manifest = validCapabilityManifest();
    $manifest['ssl'] = ['state' => 'partial'];

    expect(fn () => CapabilityManifest::from($manifest))
        ->toThrow(InvalidArgumentException::class, 'Partial capability ssl requires non-empty notes or limitations');
});

test('accepts partial capabilities explained by notes alone', function (): void {
    $manifest = validCapabilityManifest();
    $manifest['ssl'] = ['state' => 'partial', 'notes' => 'Wildcard certificates require manual DNS validation.'];

    expect(CapabilityManifest::from($manifest)->toArray())->toBe($manifest);
});

test('rejects malformed capability metadata', function (): void {
    $manifest = validCapabilityManifest();
    $manifest['ssl'] = ['state' => 'partial', 'limitations' => 'Manual configuration'];

    expect(fn () => CapabilityManifest::from($manifest))
        ->toThrow(InvalidArgumentException::class, 'Capability ssl limitations must be a non-empty list of strings');
});
