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

## Creating a Provider

1. Create a new repository: `shippercli/provider-{name}`
2. Add `composer.json` with `"type": "shipper-plugin"`
3. Implement the interfaces
4. Add `extra.shipper-plugin` pointing to your plugin class

## License

MIT