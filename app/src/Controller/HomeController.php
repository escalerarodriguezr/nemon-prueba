<?php
declare(strict_types=1);

namespace SimplexWeb\Controller;

use SimplexWeb\Service\GoogleSearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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
        $session = $request->getSession();

        if($filter === null){
            $data = $session->get('domains', []);
            return new Response(
                $this->twig->render('home.html', ["data" => $data, 'filter' => '']),
                Response::HTTP_OK
            );
        }

        //Servicio que procesa los resultados y los guarda en la session
        $this->googleSearchService->search($filter);

        $data = $session->get('domains');

        return new Response(
        $this->twig->render('home.html', ["data"=>$data, "filter" => $filter]),
        Response::HTTP_OK
        );

    }

}