# Shipper CLI Contracts

![Shipper Banner](https://raw.githubusercontent.com/shippercli/assets/main/banner.png)

Contracts and interfaces for Shipper CLI providers.

## Packages

- `shippercli/contracts` - Shared interfaces for provider plugins
- `shippercli/provider-ploi` - Ploi provider plugin
- `shippercli/provider-forge` - Laravel Forge provider plugin

## Interfaces

- `DeploymentProviderInterface` - Main provider interface
- `AliasManagerInterface` - Domain alias management
- `DeployScriptManagerInterface` - Deployment script management
- `EnvironmentManagerInterface` - Environment variable management
- `SslManagerInterface` - SSL certificate management
- `ProviderCapabilitiesInterface` - Explicit supported, partial, and unsupported capabilities
- `ServerLifecycleProviderInterface` - Safe provider server resolution, creation, inspection, and deletion
- `ShipperPluginInterface` - Plugin entry point

Provider contracts accept Shipper configuration objects as `object` so provider
packages do not depend on the CLI's internal DTO namespace. Providers should use
the documented configuration accessors (`name()`, `path()`, `repository()`,
`profiles()`, `get()`, and related feature accessors) and feature-detect optional
accessors with `method_exists()`.

Providers that expose optional features should implement the matching contract
interfaces rather than adding provider-specific methods to the core deployment
interface. Server deletion must require proof that Shipper created the server.

## Resource ownership

Resolving a resource by domain or name does not prove ownership. Before changing
an existing resource during `apply`, a provider must verify its persisted Shipper
ownership evidence and the intended project/profile scope. A name collision with
an unmanaged resource must fail before changing its environment, repository,
workloads, scheduled tasks, or deployment state. Providers must not silently add
an ownership marker to an unmanaged resource to bypass this check.

`plan` must identify unmanaged collisions as blocked actions. `destroy` and
preview cleanup require the same ownership evidence and must preserve unmanaged
resources. Missing owned resources may be treated as already removed. Automatic
adoption is not part of the deployment contract; any future adoption operation
must be explicit and separately reviewed.

## Blocked deployment plans

`DeploymentProviderInterface::plan()` is read-only. It may inspect remote state,
so provider docs must describe required API access. A known blocking prerequisite
must return boolean `blocked: true` with human-readable `actions` explaining what
needs review. A blocked plan must not include executable deployment actions or
secrets. If required ownership evidence cannot be read, the plan must be blocked
or fail explicitly; it must not assume that the resource is absent or owned.

```php
return [
    'provider' => 'example',
    'project' => $project->name(),
    'profile' => $profile->name(),
    'blocked' => true,
    'actions' => ['BLOCKED: Existing site lacks matching project/profile ownership.'],
];
```

Consumers must stop before deployment when `blocked` is true. The flag must be a
boolean; a present non-boolean value is invalid and must fail closed. Omitting
the field preserves legacy plan compatibility, and explicit `false` means the
provider found no blocker in the checks it performed. Neither value grants
permission to bypass validation or ownership checks during apply. `--force` skips
interactive confirmation only; it does not override a blocked plan.

Apply must recheck mutable prerequisites even after an unblocked plan. Providers
must document checks deferred to apply; an unblocked plan alone does not prove
deployment readiness or live connectivity. These semantics require a CLI version
that enforces them; the proposed implementation is
[CLI PR #154](https://github.com/shippercli/cli/pull/154).

## Database credentials

Creating a database is successful only when its intended application can obtain
the connection details. Providers must deliver the host, database, username, and
password through the application's environment or a supported secret reference
before deployment. Generated credentials must not appear in logs, plans, error
messages, or debug output.

Credentials belong to each database. A shared `database_password` may be used
only when explicitly configured; otherwise generated passwords must be unique
to each newly created database. Multiple databases require an explicit mapping
to distinct environment variables or secret references. A provider that can
configure only the primary database must describe that limitation and reject
additional declarations it cannot connect to the application.

Re-applying an existing database must preserve its password and the application's
working connection configuration. Providers must not generate a replacement
password without updating the database and application together. If the provider
cannot retrieve existing credentials, it must reuse the application's existing
secret or require explicit configured credentials. A missing connection or an
unprovable site association must produce an actionable failure rather than a
successful deployment with an unusable database.

## Creating a Provider

1. Create a new repository: `shippercli/provider-{name}`
2. Add `composer.json` with `"type": "shipper-plugin"`
3. Implement `DeploymentProviderInterface`
4. Implement `ShipperPluginInterface` and return a provider-name-to-class map
5. Add `extra.shipper-plugin` pointing to your plugin class

```php
public function providers(): array
{
    return [
        'example' => ExampleProvider::class,
    ];
}
```

## License

MIT
