<?php

namespace App\Service;

use SimpleXMLElement;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function PHPSTORM_META\type;

class ApiManager
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClientInterface)
    {
        $this->httpClient = $httpClientInterface;
    }

    /**
     * Give back SimpleXMLElement for the given book
     * 
     * @param string $isbn Book ISBN
     * 
     * @return SimpleXMLElement
     */
    public function fetchByISBN(string $isbn)
    {
        $response = $this->httpClient->request(
            'GET',
            'http://catalogue.bnf.fr/api/SRU?version=1.2&operation=searchRetrieve&query=bib.isbn%20adj%20"' . "$isbn" . '"',
        );

        // Get content form the API in xml
        $content = $response->getContent();
        //dd($content);


        // Transform xml content in SimpleXMLElement in order to access data
        $responseToSimpleXMLElement = new SimpleXMLElement($content);

        // Namespace recording for namespace in API's xml file 

        $ns = $responseToSimpleXMLElement->getDocNamespaces(true);
        foreach ( $ns as $prefix => $URI )   {
            $responseToSimpleXMLElement->registerXPathNamespace($prefix, $URI);
        }
        $responseToSimpleXMLElement->registerXPathNamespace('mxc', 'info:lc/xmlns/marcxchange-v2');

/*         // Encode in JSON
        $j_obj = json_encode($responseToSimpleXMLElement->xpath('//srw:records/srw:record/srw:recordData/mxc:record/*'));
        $array = json_decode($j_obj,TRUE);
        dd($array);
 */
        return $responseToSimpleXMLElement;
    }

    /**
     * Give back cover URL for the given book
     * 
     * @param string $isbn Book ISBN
     * 
     * @return string Book's URL
     */
    public function fetchCover(string $isbn)
    {
        $content = $this->fetchByISBN($isbn);

        // Ark : "//srw:recordIdentifier"
        $arkArray = $content->xpath("//srw:recordIdentifier");

        // Does the cover exist ?
        if (!array_key_exists(0, $arkArray)) {
            return null;
        }

        $ark = $arkArray[0]->__toString();
        $coverUrl = "https://catalogue.bnf.fr/couverture?&appName=NE&idArk={$ark}&couverture=1";
        echo "Ark : {$ark}<br/>";
        echo "Couverture : {$coverUrl} <br/>";

        return $coverUrl;
    }
}
