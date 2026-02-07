<?php

declare(strict_types=1);

namespace TimestampBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class TimestampBundle extends AbstractBundle
{
    /**
     * FIX: Cette méthode était présente au commit 3e9f27a mais a été supprimée
     * au commit 6d48319 ("Utilisation attribut").
     * 
     * L'attribut #[AsDoctrineListener] ne suffit PAS à enregistrer un service.
     * Il faut d'abord que la classe soit déclarée comme service Symfony.
     * 
     * Sans cette méthode, le TimestampableSubscriber n'est jamais instancié
     * et les événements prePersist/preUpdate ne sont jamais appelés.
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
    }
}
