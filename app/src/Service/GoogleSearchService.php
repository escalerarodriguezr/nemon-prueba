<?php
declare(strict_types=1);

namespace SimplexWeb\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use voku\helper\HtmlDomParser;

class GoogleSearchService
{

    private Session $session;

    public function __construct()
    {
        global $container;
        $this->session = $container->get('session');

    }


    public function search(string $filter): void
    {
        $response= file_get_contents(sprintf('http://www.google.com/search?%s',http_build_query(['q'=>$filter])));
        $dom = HtmlDomParser::str_get_html($response);
        $elements = $dom->find('div.sCuL3 div.BNeawe');

        //Array con los links obtenidos
        $arrayLinks=[];
        foreach ($elements as $element){
            $arrayLinks[] = explode(' ', $element->innerhtml)[0];
        }

        //Array con los links unicos obtenidos indicando el numero de ocurrencias
        $domainsAndNumber=[];
        foreach ($arrayLinks as $link){
            if(!isset($domainsAndNumber[$link])){
                $domainsAndNumber[$link]['name'] = $link;
                $domainsAndNumber[$link]['number'] = 1;

            }else{
                $domainsAndNumber[$link]['name'] = $link;
                $domainsAndNumber[$link]['number'] += 1;
            }
        }



        $domainSessionLinksArray = $this->session->get('domains',[]);

        $resultsSession = $this->mergeDomainSessionLinksAndQueryLinks($domainSessionLinksArray,$domainsAndNumber);

        //Ordenamos
        usort($resultsSession, function ($a,$b){
            if($a['number'] == $b['number']){
                return 0;
            }
            if($a['number']>$b['number']){
                return -1;
            }
            return 1;

        });

        //Guardamos en la session
        $this->session->set('domains',$resultsSession);


    }

    private function mergeDomainSessionLinksAndQueryLinks(array $session, array $query): array
    {
        //Transformar el array query en un array asociativo
        foreach ($query as $queryItem){
            $resultQuery[$queryItem['name']] = $queryItem;
        }

        //Transformar el array de session en un array asociativo
        $resultSession = [];
        foreach ($session as $sessionItem){
            $resultSession[$sessionItem['name']] = $sessionItem;
        }


        //Añdimos al resultado de la query los existentes en la session
        foreach ($resultQuery as $resultQueryItem){
            $name = $resultQueryItem['name'];
            if(array_key_exists($name,$resultSession)){
                $resultSession[$name]['number'] += $resultQueryItem['number'];
            }else{
                $resultSession[$name] = $resultQueryItem;
            }
        }

        return $resultSession;

    }


}