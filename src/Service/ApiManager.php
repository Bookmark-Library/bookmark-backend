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
     * @param string $isbn
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
        foreach ($ns as $prefix => $URI) {
            $responseToSimpleXMLElement->registerXPathNamespace($prefix, $URI);
        }
        $responseToSimpleXMLElement->registerXPathNamespace('mxc', 'info:lc/xmlns/marcxchange-v2');

        return $responseToSimpleXMLElement;
    }

    /**
     * Give Book in array
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return array $book
     */
    public function getBook($xml)
    {
        $book = [];
        $isbn = $this->getISBN($xml);
        $title = $this->getTitle($xml);
        $author = $this->getAuthor($xml);
        $editor = $this->getEditor($xml);
        $collection = $this->getCollection($xml);
        $date = $this->getDate($xml);
        $price = $this->getPrice($xml);
        $pages = $this->getPages($xml);
        $ark = $this->getArk($xml);
        $summary = $this->getSummary($xml);

        $book["isbn"] = $isbn ;
        $book["title"] = $title ;
        $book["authors"] = $author ;
        $book["editor"] = $editor ;
        $book["collection"] = $collection ;
        $book["publication_date"] = $date ;
        $book["price"] = $price ;
        $book["pages"] = $pages ;
        $book["image"] = $ark ;
        $book["summary"] = $summary ;

        return $book ;
    }

    /**
     * Give back ISBN for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $isbn 
     */
    private function getISBN($xml)
    {
        // ISBN : "//mxc:datafield[@tag='073']/mxc:subfield[@code='a']"
        $isbnArray = $xml->xpath("//mxc:datafield[@tag='073']/mxc:subfield[@code='a']");
        // Does the isbn exist ?
        if (!array_key_exists(0, $isbnArray)) {
            $isbn = null;
        } else {
            $isbn = $isbnArray[0]->__toString();
        }

        return $isbn; 
    }

    /**
     * Give back the title for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $title
     */
    private function getTitle($xml)
    {
        // Title: "//mxc:datafield[@tag='200']/mxc:subfield[@code='a']"
        $titleArray = $xml->xpath("//mxc:datafield[@tag='200']/mxc:subfield[@code='a']");

        if (!array_key_exists(0, $titleArray)) {
            $title = null;
        } else {
            $title = $titleArray[0]->__toString();
        }

        return $title;
    }

    /**
     * Give back the author for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return array $author
     */
    private function getAuthor($xml)
    {
        // Author lastname : "//mxc:datafield[@tag='700']/mxc:subfield[@code='a']"
        // Author firstname : "//mxc:datafield[@tag='700']/mxc:subfield[@code='b']"
        // Author2 lastname : "//mxc:datafield[@tag='701']/mxc:subfield[@code='a']"
        // Author2 firstname : "//mxc:datafield[@tag='701']/mxc:subfield[@code='b']"
        $author = [];

        // Author 1
        $authorLastnameArray = $xml->xpath("//mxc:datafield[@tag='700']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $authorLastnameArray)) {
            $author [0]['lastname'] = null;
        } else {
            $authorLastname = $authorLastnameArray[0]->__toString();
            $author [0]['lastname'] = $authorLastname ;
        }

        $authorFirstnameArray = $xml->xpath("//mxc:datafield[@tag='700']/mxc:subfield[@code='b']");
        if (!array_key_exists(0, $authorFirstnameArray)) {
            $author [0]['firstname'] = null;
        } else {
            $authorFirstname = $authorFirstnameArray[0]->__toString();
            $author [0]['firstname'] = $authorFirstname ;
        }

        // Author 2
        $authorLastnameArray = $xml->xpath("//mxc:datafield[@tag='702']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $authorLastnameArray)) {
            return $author;
        } else {
            $authorLastname = $authorLastnameArray[0]->__toString();
            $author [1]['lastname'] = $authorLastname ;
        }

        $authorFirstnameArray = $xml->xpath("//mxc:datafield[@tag='702']/mxc:subfield[@code='b']");
        if (!array_key_exists(0, $authorFirstnameArray)) {
            $author [1]['firstname'] = null;
        } else {
            $authorFirstname = $authorFirstnameArray[0]->__toString();
            $author [1]['firstname'] = $authorFirstname ;
        }

        return $author;
    }

    /**
     * Give back the editor for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $editor
     */
    private function getEditor($xml)
    {
        // Editor : "//mxc:datafield[@tag='210']/mxc:subfield[@code='c']"
        // Editor : "//mxc:datafield[@tag='214']/mxc:subfield[@code='c']" + [ind2 ='0']
        $editorArray = $xml->xpath("//mxc:datafield[@tag='210']/mxc:subfield[@code='c']");
        if (!array_key_exists(0, $editorArray)) {
            $editorArrayBis = $xml->xpath("//mxc:datafield[@tag='214']/mxc:subfield[@code='c']");
            if (!array_key_exists(0, $editorArrayBis)) {
                $editor = null ; 
            } else {
                $editor = $editorArrayBis[0]->__toString();
            }
        } else {
            $editor = $editorArray[0]->__toString();
        }

        return $editor;
    }
    
    /**
     * Give back the collection for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $collection
     */
    private function getCollection($xml)
    {
        // Collection : "//mxc:datafield[@tag='225']/mxc:subfield[@code='a']"
        // Collection : "//mxc:datafield[@tag='225']/mxc:subfield[@code='i']"
        $collectionArray = $xml->xpath("//mxc:datafield[@tag='225']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $collectionArray)) {
            $collectionArrayBis = $xml->xpath("//mxc:datafield[@tag='225']/mxc:subfield[@code='i']");
            if (!array_key_exists(0, $collectionArrayBis)) {
                $collection = null;
            } else {
                $collection = $collectionArrayBis[0]->__toString();
            }
        } else {
            $collection = $collectionArray[0]->__toString();
        }

        return $collection;

    }

    /**
     * Give back the publication date for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $date
     */
    private function getDate($xml)
    {
        // Date : "//mxc:datafield[@tag='210']/mxc:subfield[@code='d']"
        // Date : "//mxc:datafield[@tag='214']/mxc:subfield[@code='d']"
        $dateArray = $xml->xpath("//mxc:datafield[@tag='210']/mxc:subfield[@code='d']");
        if (!array_key_exists(0, $dateArray)) {
            $dateArrayBis = $xml->xpath("//mxc:datafield[@tag='214']/mxc:subfield[@code='d']");
            if (!array_key_exists(0, $dateArrayBis)) {
                $date = null ;
            } else {
                $date = $dateArrayBis[0]->__toString();
                $date = intval(preg_replace('/[^0-9]/', '', $date));
            }
        } else {
            $date = $dateArray[0]->__toString();
            $date = intval(preg_replace('/[^0-9]/', '', $date));

        }

        return $date ;
    }

    /**
     * Give back the price for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $price
     */
    private function getPrice($xml)
    {
        // Price : "//mxc:datafield[@tag='010']/mxc:subfield[@code='d']"
        $priceArray = $xml->xpath("//mxc:datafield[@tag='010']/mxc:subfield[@code='d']");
        if (!array_key_exists(0, $priceArray)) {
            $price = null;
        } else {
            $price = $priceArray[0]->__toString();
            //$price = floatval(preg_replace('/[^0-9,.]/', '', $price));

        }
    
        return $price;
    }

        /**
     * Give back the pages for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $pages
     */
    private function getPages($xml)
    {
        // Pages : "//mxc:datafield[@tag='215']/mxc:subfield[@code='a']"
        $pageArray = $xml->xpath("//mxc:datafield[@tag='215']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $pageArray)) {
            $pages = null;
        } else {
            $pages = $pageArray[0]->__toString();
            $pages = preg_replace('/[^0-9,.]/', '', $pages);
            $pages = explode('.', $pages);
            $pages = intval($pages[1]);
           
        }
    
        return $pages;
    }

    /**
     * Give back cover URL for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $coverUrl
     */
    private function getArk($xml)
    {
        // Ark : "//srw:recordIdentifier"
        $arkArray = $xml->xpath("//srw:recordIdentifier");
        if (!array_key_exists(0, $arkArray)) {
            $coverUrl = null ;
        } else {
            $ark = $arkArray[0]->__toString();
            $coverUrl = "https://catalogue.bnf.fr/couverture?&appName=NE&idArk={$ark}&couverture=1";
        }

        return $coverUrl;
    }

    /**
     * Give back the summary for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string $summary
     */
    private function getSummary($xml)
    {
        // Summary : "//mxc:datafield[@tag='330']/mxc:subfield[@code='a']"
        // Summary : "//mxc:datafield[@tag='339']/mxc:subfield[@code='a']"
        $summaryArray = $xml->xpath("//mxc:datafield[@tag='330']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $summaryArray)) {
            $summaryArrayBis = $xml->xpath("//mxc:datafield[@tag='339']/mxc:subfield[@code='a']");
            if (!array_key_exists(0, $summaryArrayBis)) {   
                $summary = null;
            } else {
                $summary = $summaryArrayBis[0]->__toString();
            }
        } else {
            $summary = $summaryArray[0]->__toString();
        }

        return $summary;
    }

}
