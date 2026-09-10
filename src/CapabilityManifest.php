<?php

declare(strict_types=1);

namespace ShipperCli\Contracts;

use InvalidArgumentException;

final readonly class CapabilityManifest
{
    /** @var list<string> */
    public const CAPABILITIES = [
        'app_deploy',
        'domain_management',
        'ssl',
        'env',
        'databases',
        'profiles',
        'background_workloads',
        'observability',
        'rollback',
        'previews',
        'server_lifecycle',
    ];

    /** @var list<string> */
    public const STATES = ['supported', 'partial', 'unsupported'];

    /** @param array<string, array{state: string, notes?: string, requirements?: list<string>, limitations?: list<string>}> $capabilities */
    private function __construct(private array $capabilities) {}

    /** @param array<string, mixed> $capabilities */
    public static function from(array $capabilities): self
    {
        $missing = \array_values(\array_diff(self::CAPABILITIES, \array_keys($capabilities)));
        if ($missing !== []) {
            throw new InvalidArgumentException('Missing capabilities: '.\implode(', ', $missing));
        }

        $unknown = \array_values(\array_diff(\array_keys($capabilities), self::CAPABILITIES));
        if ($unknown !== []) {
            throw new InvalidArgumentException('Unknown capabilities: '.\implode(', ', $unknown));
        }

        foreach (self::CAPABILITIES as $capability) {
            $entry = $capabilities[$capability];
            if (! \is_array($entry)) {
                throw new InvalidArgumentException("Capability {$capability} must be an array");
            }

            $state = $entry['state'] ?? null;
            if (! \is_string($state) || ! \in_array($state, self::STATES, true)) {
                throw new InvalidArgumentException("Capability {$capability} has an invalid state");
            }

            self::validateTextMetadata($capability, 'notes', $entry['notes'] ?? null);
            self::validateTextMetadata($capability, 'requirements', $entry['requirements'] ?? null);
            self::validateTextMetadata($capability, 'limitations', $entry['limitations'] ?? null);

            if ($state === 'partial' && ! self::hasExplanation($entry)) {
                throw new InvalidArgumentException("Partial capability {$capability} requires non-empty notes or limitations");
            }
        }

        /** @var array<string, array{state: string, notes?: string, requirements?: list<string>, limitations?: list<string>}> $capabilities */
        return new self($capabilities);
    }

    /** @return array<string, array{state: string, notes?: string, requirements?: list<string>, limitations?: list<string>}> */
    public function toArray(): array
    {
        return $this->capabilities;
    }

    /** @param mixed $value */
    private static function validateTextMetadata(string $capability, string $key, mixed $value): void
    {
        if ($value === null) {
            return;
        }

        if ($key === 'notes') {
            if (! \is_string($value) || \trim($value) === '') {
                throw new InvalidArgumentException("Capability {$capability} {$key} must be a non-empty string");
            }

            return;
        }

        if (! \is_array($value) || $value === []) {
            throw new InvalidArgumentException("Capability {$capability} {$key} must be a non-empty list of strings");
        }

        foreach ($value as $item) {
            if (! \is_string($item) || \trim($item) === '') {
                throw new InvalidArgumentException("Capability {$capability} {$key} must be a non-empty list of strings");
            }
        }
    }

    /** @param array<string, mixed> $entry */
    private static function hasExplanation(array $entry): bool
    {
        return (\is_string($entry['notes'] ?? null) && \trim($entry['notes']) !== '')
            || (\is_array($entry['limitations'] ?? null) && $entry['limitations'] !== []);
    }
}
