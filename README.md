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
adoption is not part of the deployment contract. A provider that implements
adoption must require explicit operator authorization tied to the exact
resource identity and project/profile scope. The plan must identify each
resource that will be adopted and the changes that will be made to it; `--force`
alone is not adoption authorization. Apply must recheck the identity and scope
before changing the resource, then persist an auditable `adopted` state for that
resource. Adoption must not silently convert it to a Shipper-created resource.

Lifecycle state is per resource, including child resources associated with an
adopted parent. Destroy and preview cleanup must preserve adopted resources.
They may remove a child resource only when Shipper separately recorded that it
created that exact child in the current scope and the normal ownership checks
pass. Deleting an adopted resource requires a separate explicit ownership
transfer or deletion operation, recorded against that exact resource before
destruction; parent adoption does not authorize recursive deletion. Providers
that cannot persist and verify this lifecycle state must refuse adoption and
preserve the resource.

### Readable ownership evidence

Ownership requires a successful, structurally valid response for the exact
resource being considered. Explicit API failure/error fields invalidate the
response even when it also contains a matching marker. Missing content is not
an empty file and cannot establish absence, ownership or permission to replace
existing configuration.

Environment-backed ownership checks must parse the complete document using the
application's effective assignment rules. Duplicate assignments, quoted values
and multiline values must be handled consistently. A marker parsed before an
unfinished trailing value does not establish ownership; parsing a valid prefix
is insufficient. Raw matching lines inside quoted text do not establish it either.

Required refusal regressions include matching markers with an incomplete trailing
value, an explicit API error with matching content, duplicate conflicting marker
assignments, and a marker embedded in a multiline value. Tests must verify no
mutation/deletion request is sent, alongside valid multiline and explicitly empty
content controls. Empty content can be readable without proving ownership.

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

### Persistence and partial operations

A successful write response alone does not prove credential delivery. Verify
persisted selected values through the provider's supported read path before
reporting success or starting dependent operations. Refuse unreadable, incomplete
or mismatching readback without logging secrets. Document persistence timing and
retry behavior; stale readback must not be treated as proof that creation failed.

For multiple databases, deliver each confirmed connection before creating the
next database. A later failure must preserve earlier stored connections, and a
fresh process must be able to reuse confirmed resources without rotating passwords.
Configuration steps must run sequentially and stop at the first failed prerequisite.

Creation and delivery can still fail between operations. Providers must document
ambiguous creation, failed storage and partial cleanup outcomes, including a
recovery procedure that inspects actual resources before retrying. Incremental
writes do not provide an atomic transaction, durable credential journal or
concurrency control. Those guarantees require separate implementation and evidence.

Required regressions include an acknowledged but ignored write, unreadable
readback, a second creation failing after the first connection is stored, a fresh
retry creating only missing resources, and failure preventing later dependent
mutations. Validate literal secret values and unrelated configuration preservation.

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
