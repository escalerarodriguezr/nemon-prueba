<?php

use Framework\EventSubscriber\ContentLengthSubscriber;
use SimplexWeb\Service\GoogleSearchService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$containerBuilder = new ContainerBuilder();

$containerBuilder->register('context', RequestContext::class);
if (isset($routes)) {
    $containerBuilder->register('matcher', UrlMatcher::class)
        ->setArguments([$routes, new Reference('context')]);
}

$containerBuilder->register('controller_resolver', ControllerResolver::class);
$containerBuilder->register('argument_resolver', ArgumentResolver::class);

//Framework subscribers
$containerBuilder->register('event-subscriber.response_content_length', ContentLengthSubscriber::class);

$containerBuilder->register('dispatcher', EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('event-subscriber.response_content_length')]);


$containerBuilder->register('simplex', \Framework\Simplex::class)
    ->setArguments([
        new Reference('dispatcher'),
        new Reference('matcher'),
        new Reference('controller_resolver'),
        new Reference('argument_resolver'),
    ]);

$containerBuilder->register('twig_loader',FilesystemLoader::class)
    ->setArguments([TEMPLATE_PATH]);
$containerBuilder->register('twig',Environment::class)
    ->setArguments([new Reference('twig_loader'), ['cache'=>false]]);

$containerBuilder->register('php_session_bridge',PhpBridgeSessionStorage::class);
$containerBuilder->register('session',Session::class)
    ->setArguments([ new Reference('php_session_bridge')])
    ->addMethodCall('start');

//Services
$containerBuilder->register('google-search',GoogleSearchService::class);

return $containerBuilder;
