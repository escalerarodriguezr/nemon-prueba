<?php
declare(strict_types=1);

namespace SimplexWeb\Service;

use voku\helper\HtmlDomParser;

class GoogleSearchService
{
    public function search(string $filter): array
    {
        $response= file_get_contents(sprintf('http://www.google.com/search?%s',http_build_query(['q'=>$filter])));
        $dom = HtmlDomParser::str_get_html($response);
        $elements = $dom->find('div.sCuL3 div.BNeawe');

        $arrayLinks=[];
        foreach ($elements as $element){
            $arrayLinks[] = explode(' ', $element->innerhtml)[0];
        }

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

        usort($domainsAndNumber, function ($a,$b){
            if($a['number'] == $b['number']){
                return 0;
            }
            if($a['number']>$b['number']){
                return -1;
            }
            return 1;

        });;

        return $domainsAndNumber;
    }


}