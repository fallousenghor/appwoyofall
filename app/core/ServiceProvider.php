<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;

class ServiceProvider
{
    private Container $container;
    private array $config;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->loadConfig();
    }

    /**
     * Charger la configuration YAML
     */
    private function loadConfig(): void
    {
        $configPath = dirname(__DIR__) . '/config/services.yml';
        if (!file_exists($configPath)) {
            throw new \Exception("Fichier de configuration des services introuvable: $configPath");
        }

        $yamlConfig = Yaml::parseFile($configPath);
        // Remplacement des variables d'environnement dans la config YAML
        $replaceEnv = function ($value) {
            if (is_string($value) && preg_match('/^\$\{([A-Z0-9_]+)(?::([^}]*))?}$/', $value, $m)) {
                $env = $m[1];
                $default = isset($m[2]) ? $m[2] : null;
                return $_ENV[$env] ?? $default;
            }
            return $value;
        };
        if (isset($yamlConfig['services']['database'])) {
            foreach ($yamlConfig['services']['database'] as $k => $v) {
                $yamlConfig['services']['database'][$k] = $replaceEnv($v);
            }
        }
        $this->config = $yamlConfig['services'] ?? [];
    }

    /**
     * Enregistrer tous les services depuis la configuration YAML
     */
    public function register(): void
    {
        // Enregistrer une instance PDO globale
        $this->registerPDO();

        // Enregistrer les interfaces avec leurs implémentations (d'abord !)
        $this->registerInterfaces();

        // Enregistrer les repositories
        $this->registerCategory('repositories');

        // Enregistrer les services
        $this->registerCategory('services');

        // Enregistrer les controllers
        $this->registerCategory('controllers');
    }

    /**
     * Enregistrer une instance PDO dans le container
     */
    private function registerPDO(): void
    {
        $dbConf = $this->config['database'] ?? [];
        // Support d'un DSN direct si défini dans l'environnement ou la config
        $dsn = $dbConf['dsn'] ?? ($_ENV['DSN'] ?? (isset($dbConf['host'], $dbConf['port'], $dbConf['name'])
            ? sprintf('pgsql:host=%s;port=%s;dbname=%s', $dbConf['host'], $dbConf['port'], $dbConf['name'])
            : null));
        $user = $dbConf['user'] ?? ($_ENV['DB_USER'] ?? null);
        $password = $dbConf['password'] ?? ($_ENV['DB_PASSWORD'] ?? null);
        if ($dsn && $user !== null && $password !== null) {
            $pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
            $this->container->instance(\PDO::class, $pdo);
        }
    }

    /**
     * Enregistrer une catégorie de services
     */
    private function registerCategory(string $category): void
    {
        if (!isset($this->config[$category])) {
            return;
        }

        foreach ($this->config[$category] as $serviceName => $serviceConfig) {
            $className = $serviceConfig['class'];
            $isSingleton = $serviceConfig['singleton'] ?? false;

            if ($isSingleton) {
                $this->container->singleton($className, $className);
            } else {
                $this->container->bind($className, $className);
            }

            // Enregistrer aussi avec le nom court pour compatibilité
            if ($isSingleton) {
                $this->container->singleton($serviceName, $className);
            } else {
                $this->container->bind($serviceName, $className);
            }
        }
    }

    /**
     * Obtenir la configuration des dépendances pour un service
     */
    public function getDependencies(string $serviceName): array
    {
        foreach (['repositories', 'services', 'controllers'] as $category) {
            // Recherche FQCN
            if (isset($this->config[$category][$serviceName]['dependencies'])) {
                return $this->config[$category][$serviceName]['dependencies'];
            }
            // Recherche nom court
            $short = ($p = strrpos($serviceName, '\\')) !== false ? substr($serviceName, $p + 1) : $serviceName;
            if (isset($this->config[$category][$short]['dependencies'])) {
                return $this->config[$category][$short]['dependencies'];
            }
        }
        return [];
    }

    /**
     * Enregistrer les interfaces avec leurs implémentations
     */
    private function registerInterfaces(): void
    {
        if (isset($this->config['interfaces'])) {
            foreach ($this->config['interfaces'] as $interface => $implementation) {
                $this->container->singleton($interface, $implementation);
            }
        }
    }

    /**
     * Obtenir la configuration complète
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
