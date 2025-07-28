<?php

namespace App\Core;

use App\Core\Abstract\Singleton;

class App extends Singleton
{
    private static ?Container $container = null;
    private static bool $initialized = false;

    public static function initialize(): void
    {
        if (!self::$initialized) {
            self::$container = Container::getInstance();
            
            // Enregistrer les services
            $serviceProvider = new ServiceProvider(self::$container);
            $serviceProvider->register();
            
            self::$initialized = true;
        }
    }

    public static function get(string $serviceName)
    {
        self::initialize();
        
        // Mapping des anciens noms vers les nouvelles classes
        $serviceMap = [
            'CitoyenController' => \App\Controller\CitoyenController::class,
            'AchatController' => \App\Controller\AchatController::class,
            'CitoyenService' => \App\Service\CitoyenService::class,
            'AchatService' => \App\Service\AchatService::class,
            'LoggerService' => \App\Service\LoggerService::class,
            'CitoyenRepository' => \App\Repository\CitoyenRepository::class,
            'AchatRepository' => \App\Repository\AchatRepository::class,
            'CompteurRepository' => \App\Repository\CompteurRepository::class,
            'TrancheRepository' => \App\Repository\TrancheRepository::class,
            'LoggerRepository' => \App\Repository\LoggerRepository::class,
        ];

        // Si le nom ne contient pas de backslash, on tente le mapping, sinon on prend le nom tel quel
        if (isset($serviceMap[$serviceName])) {
            $className = $serviceMap[$serviceName];
        } elseif (strpos($serviceName, '\\') === false) {
            // On tente de deviner le FQCN pour les services/controllers classiques
            if (class_exists('App\\Controller\\' . $serviceName)) {
                $className = 'App\\Controller\\' . $serviceName;
            } elseif (class_exists('App\\Service\\' . $serviceName)) {
                $className = 'App\\Service\\' . $serviceName;
            } elseif (class_exists('App\\Repository\\' . $serviceName)) {
                $className = 'App\\Repository\\' . $serviceName;
            } else {
                $className = $serviceName;
            }
        } else {
            $className = $serviceName;
        }

        return self::$container->resolve($className);
    }

    public static function getContainer(): Container
    {
        self::initialize();
        return self::$container;
    }

    // Pour compatibilité descendante
    public static function getDependency(string $key): mixed
    {
        return self::get($key);
    }
}
