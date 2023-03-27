<?php
declare(strict_types=1);

namespace SimplexWeb\Controller;

use SimplexWeb\Service\GoogleSearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
class HomeController
{
    private Environment $twig;
    private GoogleSearchService $googleSearchService;

    public function __construct()
    {
        global $container;
        $this->twig = $container->get('twig');
        $this->googleSearchService = $container->get('google-search');

    }

    public function index(Request $request): Response
    {

        $filter = $request->get('texto');

        if($filter === null){
            return new Response(
                $this->twig->render('home.html', ["data" => null]),
                Response::HTTP_OK
            );
        }

        $data = $this->googleSearchService->search($filter);

        return new Response(
        $this->twig->render('home.html', ["data"=>$data]),
        Response::HTTP_OK
    );

    }

}