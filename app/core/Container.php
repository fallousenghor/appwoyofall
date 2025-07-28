<?php

namespace App\Core;

use ReflectionClass;
use ReflectionParameter;
use InvalidArgumentException;
use Exception;

class Container
{
    private static ?Container $instance = null;
    private array $bindings = [];
    private array $instances = [];
    private array $singletons = [];

    private function __construct() {}

    public static function getInstance(): Container
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Lier une interface ou classe à une implémentation
     */
    public function bind(string $abstract, string $concrete = null, bool $singleton = false): void
    {
        $concrete = $concrete ?? $abstract;
        
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton
        ];

        if ($singleton) {
            $this->singletons[] = $abstract;
        }
    }

    /**
     * Lier un singleton
     */
    public function singleton(string $abstract, string $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Résoudre une dépendance
     */
    public function resolve(string $abstract)
    {
        // Si c'est un singleton déjà instancié, le retourner
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Obtenir la classe concrète
        $concrete = $this->getConcrete($abstract);

        // Construire l'instance
        $instance = $this->build($concrete);

        // Si c'est un singleton, le stocker
        if ($this->isSingleton($abstract)) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Construire une instance avec injection de dépendances
     */
    private function build(string $concrete)
    {
        $reflectionClass = new ReflectionClass($concrete);

        if (!$reflectionClass->isInstantiable()) {
            throw new InvalidArgumentException("La classe $concrete n'est pas instanciable");
        }

        $constructor = $reflectionClass->getConstructor();

        // Si pas de constructeur, retourner une instance simple
        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        // Injection avancée : si ServiceProvider existe, utiliser les dépendances YAML
        $serviceProvider = null;
        if (class_exists('App\\Core\\App') && method_exists('App\\Core\\App', 'getContainer')) {
            $container = \App\Core\App::getContainer();
            foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
                if (isset($trace['object']) && $trace['object'] instanceof \App\Core\ServiceProvider) {
                    $serviceProvider = $trace['object'];
                    break;
                }
            }
            if (!$serviceProvider && class_exists('App\\Core\\ServiceProvider')) {
                $serviceProvider = new \App\Core\ServiceProvider($container);
            }
        }

        if ($serviceProvider && method_exists($serviceProvider, 'getDependencies')) {
            $serviceName = $reflectionClass->getName();
            $deps = $serviceProvider->getDependencies($serviceName);
            error_log("[Container] Création de $serviceName, dépendances YAML: " . json_encode($deps));
            if (!empty($deps)) {
                $dependencies = [];
                foreach ($deps as $dep) {
                    $dependencies[] = $this->resolve($dep);
                }
                error_log("[Container] Arguments injectés dans $serviceName: " . json_encode(array_map(function($d) { return is_object($d) ? get_class($d) : gettype($d); }, $dependencies)));
                return $reflectionClass->newInstanceArgs($dependencies);
            } else {
                // Si le service est un Service ou Controller, lever une exception explicite
                if (preg_match('#App\\\\(Service|Controller)\\\\#', $serviceName)) {
                    throw new \Exception("Aucune dépendance YAML trouvée pour $serviceName. Vérifiez la clé dans services.yml (FQCN) et la méthode getDependencies.");
                }
            }
        }

        // Sinon, fallback sur l'injection classique
        $dependencies = $this->resolveDependencies($constructor->getParameters());
        error_log("[Container] Arguments injectés (fallback) dans $concrete: " . json_encode(array_map(function($d) { return is_object($d) ? get_class($d) : gettype($d); }, $dependencies)));
        return $reflectionClass->newInstanceArgs($dependencies);
    }

    /**
     * Résoudre les dépendances d'un constructeur
     */
    private function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $this->resolveDependency($parameter);
            $dependencies[] = $dependency;
        }

        return $dependencies;
    }

    /**
     * Résoudre une dépendance spécifique
     */
    private function resolveDependency(ReflectionParameter $parameter)
    {
        // Si le paramètre a une valeur par défaut et pas de type
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        // Obtenir le type du paramètre
        $type = $parameter->getType();

        if ($type === null) {
            // On ne peut pas résoudre, retourner null
            return null;
        }

        // S'assurer que le type est ReflectionNamedType (pour getName() et isBuiltin())
        if ($type instanceof \ReflectionNamedType) {
            if ($type->isBuiltin()) {
                // Pour les types primitifs, retourner null ou la valeur par défaut si disponible
                return $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
            }
            // Résoudre récursivement la classe
            return $this->resolve($type->getName());
        }

        // Si on ne peut pas déterminer le type, retourner null
        return null;
    }

    /**
     * Obtenir la classe concrète pour un abstract
     */
    private function getConcrete(string $abstract): string
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * Vérifier si c'est un singleton
     */
    private function isSingleton(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) && 
               $this->bindings[$abstract]['singleton'] === true;
    }

    /**
     * Méthode de commodité pour créer des instances
     */
    public function make(string $abstract)
    {
        return $this->resolve($abstract);
    }

    /**
     * Enregistrer une instance existante
     */
    public function instance(string $abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Vérifier si un binding existe
     */
    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }
}
