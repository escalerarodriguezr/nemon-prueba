<?php

namespace Framework;

use Framework\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Simplex
{
    public function __construct(
        private EventDispatcher $dispatcher,
        private UrlMatcher $matcher,
        private ControllerResolver $controllerResolver,
        private ArgumentResolver $argumentResolver,
        private Session $session
    )
    {
    }

    public function handle(Request $request):Response
    {

        $this->matcher->getContext()->fromRequest($request);
        $request->setSession($this->session);

        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));
            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);
            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $exception) {
            $response = new Response('Not Found', 404);
        } catch (\Exception $exception) {
            $response = new Response('An error occurred', 500);
        }

        $this->dispatcher->dispatch(new ResponseEvent($response, $request), 'response');
        return $response;
    }

}