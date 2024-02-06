<?php

namespace App\Application\Factory;


use App\Application\Settings\SettingsInterface;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;

class DatabaseManagerFactory
{

    /**
     * Constructs a new Capsule instance and sets it up to use Eloquent ORM.
     *
     * @param ContainerInterface $container The container to get the database settings from.
     *
     * @return Manager The Capsule instance.
     */
    public function __construct(ContainerInterface $container)
    {
        // Create a new Capsule instance
        $capsule = new Manager();
        $settings = $container->get(SettingsInterface::class);
        
        // Add the database connection from the container's settings
        $capsule->addConnection($settings->get('db'));

        // Set Capsule as a global instance
        $capsule->setAsGlobal();

        // Boot Eloquent ORM
        $capsule->bootEloquent();

        // Return the Capsule instance
        return $capsule;
    }
}