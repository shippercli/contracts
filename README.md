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
- `ShipperPluginInterface` - Plugin entry point

Provider contracts accept Shipper configuration objects as `object` so provider
packages do not depend on the CLI's internal DTO namespace. Providers should use
the documented configuration accessors (`name()`, `path()`, `repository()`,
`profiles()`, `get()`, and related feature accessors) and feature-detect optional
accessors with `method_exists()`.

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
