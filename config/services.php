<?php

use AcMarche\Mercredi\Jour\Tarification\Form\TarificationFormGeneratorInterface;
use AcMarche\Mercredi\Jour\Tarification\Form\TarificationHottonFormGenerator;
use AcMarche\Mercredi\Namer\DirectoryNamer;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Plaine\Calculator\PlaineCalculatorInterface;
use AcMarche\Mercredi\Plaine\Calculator\PlaineHottonCalculator;
use AcMarche\Mercredi\Presence\Calculator\PrenceHottonCalculator;
use AcMarche\Mercredi\Presence\Calculator\PresenceCalculatorInterface;
use AcMarche\Mercredi\ServiceIterator\AfterUserRegistration;
use AcMarche\Mercredi\ServiceIterator\Register;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::EMAIL_SENDER, '%env(MERCREDI_FROM)%');
    $parameters->set(Option::ACCUEIL_PRIX, '%env(MERCREDI_ACCUEIL_PRIX)%');
    $parameters->set(Option::PRESENCE_DEADLINE_DAYS, '%env(MERCREDI_PRESENCE_DEADLINE_DAYS)%');
    $parameters->set(Option::PEDAGOGIQUE_DEADLINE_DAYS, '%env(MERCREDI_PEDAGOGIQUE_DEADLINE_DAYS)%');

    /**
     * Pour envoie de mail en mode console
     */
    $parameters->set('router.request_context.scheme', '%env(MERCREDI_HTTP_SCHEME)%');
    $parameters->set('router.request_context.host', '%env(MERCREDI_HTTP_HOST)%');

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('AcMarche\Mercredi\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests2}']);

    $services->set(DirectoryNamer::class)
        ->public();

    $services->alias(TarificationFormGeneratorInterface::class, TarificationHottonFormGenerator::class);

    $services->alias(PresenceCalculatorInterface::class, PrenceHottonCalculator::class);

    $services->alias(PlaineCalculatorInterface::class, PlaineHottonCalculator::class);

    $services->alias(LoaderInterface::class, 'fidry_alice_data_fixtures.loader.doctrine');

    $services->instanceof(AfterUserRegistration::class)
        ->tag('app.user.after_registration');

    $services->set(Register::class)
        ->arg('$secondaryFlows', tagged_iterator('app.user.after_registration'));
    /*  $services->set(PresenceConstraints::class)
          ->arg('$constraints', tagged_iterator('mercredi.presence_constraint'));*/
};
