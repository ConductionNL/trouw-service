# Trouw Service chart

The Helm chart installs Trouw Service and by default the following dependencies using subcharts:

- [PostgreSQL](https://github.com/bitnami/charts/tree/master/bitnami/postgresql)
- [Redis](https://github.com/bitnami/charts/tree/master/bitnami/redis)

## Installation

First configure the Helm repository:

```bash
helm repo add trouw-service https://raw.githubusercontent.com/ConductionNL/trouw-service/master/api/helm/
helm repo update
```

Install the Helm chart with:

```bash
helm install my-trouw-service trouw-service/trouw-service --version 0.1.0
```

:warning: The default settings are unsafe for production usage. Configure proper secrets, enable persistency and consider High Availability (HA) for the database and the application.

## Configuration

| Parameter | Description | Default |
| --------- | ----------- | ------- |
| `settings.domain` | The domain (if any) that you want to deploy to | `conduction.nl` |
| `settings.subdomain` | the subdomain of the installation e.g. www. | `trouw-service` |
| `settings.subpath` | Any subpath to follow the domain, like /api/v1 | `trouw-service` |
| `settings.subpathRouting` | Whether to actualy use te supath | `false` |
| `settings.env` | Iether prod or dev, determens settings like error tracing | `dev` |
| `settings.web` | Whether tot start an ingress inway | `false` |
| `settings.debug` | Run te apllication in debu mode | 1 |
| `settings.cache` | Activate resource caching | `false` |
| `settings.corsAllowOrigin` | Set the cors header | `['*']` |
| `settings.trustedHosts` | A regex function for whitelisting ip's | '^.+$' |
| `settings.pullPolicy` | When to pull new images | `Always` |

Check [values.yaml](./values.yaml) for all the possible configuration options.

# Deploying to a Kubernetes Cluster

API Platform comes with a native integration with [Kubernetes](https://kubernetes.io/) and the [Helm](https://helm.sh/)
package manager.

[Learn how to deploy in the dedicated documentation entry](https://api-platform.com/docs/deployment/kubernetes/).
