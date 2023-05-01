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
     * SimpleXMLElement for the given book
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
     * Book in array
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return array|null $book
     */
    public function getBook($xml)
    {
        $book = [];
        $isbn = $this->getISBN($xml);
        $title = $this->getTitle($xml);

        if($title === null){
            return null;
        }
    
        $author = $this->getAuthor($xml);
        $editor = $this->getEditor($xml);
        $collection = $this->getCollection($xml);
        $date = $this->getDate($xml);
        $price = $this->getPrice($xml);
        $pages = $this->getPages($xml);
        $ark = $this->getArk($xml);
        $summary = $this->getSummary($xml);

        $book["isbn"] = $isbn;
        $book["title"] = $title;
        $book["authors"] = $author;
        $book["editor"] = $editor;
        $book["collection"] = $collection;
        $book["publication_date"] = $date;
        $book["price"] = $price;
        $book["pages"] = $pages;
        $book["image"] = $ark;
        $book["summary"] = $summary;

        return $book;
    }

    /**
     * ISBN for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $isbn 
     */
    private function getISBN($xml)
    {
        $isbnArray = $xml->xpath("//mxc:datafield[@tag='073']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $isbnArray)) {
            $isbn = null;
        } else {
            $isbn = $isbnArray[0]->__toString();
        }

        return $isbn;
    }

    /**
     * Title for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $title
     */
    private function getTitle($xml)
    {
        // Title
        $titleArray = $xml->xpath("//mxc:datafield[@tag='200']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $titleArray)) {
            $title = null;
        } else {
            $title = $titleArray[0]->__toString();
        }

        // Title volume number & Title parent's title
        // First way
        $volumeArray = $xml->xpath("//mxc:datafield[@tag='200']/mxc:subfield[@code='h']");
        if (!array_key_exists(0, $volumeArray)) {
            // Second way if first one doesn't exist
            $parentArray = $xml->xpath("//mxc:datafield[@tag='225']/mxc:subfield[@code='a']");
            $volumeArrayBis = $xml->xpath("//mxc:datafield[@tag='225']/mxc:subfield[@code='v']");

            // Does parent exist in second way ?
            if (!array_key_exists(0, $parentArray)) {
                $parent = null;
            } else {
                $parent = $parentArray[0]->__toString();
            }

            // Does volume exist in second way ?
            if (!array_key_exists(0, $volumeArrayBis)) {
                $volume = null;
            } else {
                $volume = $volumeArrayBis[0]->__toString();
            }
        } else {
            // First way values
            $volume = $volumeArray[0]->__toString();
            $parent = null;
        }

        if ($volume === null && $parent === null) {
            return $title;
        } elseif ($volume === null) {
            return $title . " - " . $parent;
        } elseif ($parent === null) {
            return $title . " (" . $volume . ")";
        } else {
            return $title . " - " . $parent . " (" . $volume . ")";
        }
    }

    /**
     * Author for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return array|null $author
     */
    private function getAuthor($xml)
    {
        $author = [];

        // Author 1
        // Lastname
        $authorLastnameArray = $xml->xpath("//mxc:datafield[@tag='700']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $authorLastnameArray)) {
            $author[0]['lastname'] = "Inconnu";
        } else {
            $authorLastname = $authorLastnameArray[0]->__toString();
            $author[0]['lastname'] = $authorLastname;
        }

        // Firstname
        $authorFirstnameArray = $xml->xpath("//mxc:datafield[@tag='700']/mxc:subfield[@code='b']");
        if (!array_key_exists(0, $authorFirstnameArray)) {
            $author[0]['firstname'] = null;
        } else {
            $authorFirstname = $authorFirstnameArray[0]->__toString();
            $author[0]['firstname'] = $authorFirstname;
        }

        // Author 2
        // Lastname
        $authorLastnameArray = $xml->xpath("//mxc:datafield[@tag='702']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $authorLastnameArray)) {
            return $author;
        } else {
            $authorLastname = $authorLastnameArray[0]->__toString();
            $author[1]['lastname'] = $authorLastname;
        }

        // Firstname
        $authorFirstnameArray = $xml->xpath("//mxc:datafield[@tag='702']/mxc:subfield[@code='b']");
        if (!array_key_exists(0, $authorFirstnameArray)) {
            $author[1]['firstname'] = null;
        } else {
            $authorFirstname = $authorFirstnameArray[0]->__toString();
            $author[1]['firstname'] = $authorFirstname;
        }

        return $author;
    }

    /**
     * Editor for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $editor
     */
    private function getEditor($xml)
    {
        // First way
        $editorArray = $xml->xpath("//mxc:datafield[@tag='210']/mxc:subfield[@code='c']");
        if (!array_key_exists(0, $editorArray)) {
            // Second way if first way doesn't exist
            $editorArrayBis = $xml->xpath("//mxc:datafield[@tag='214']/mxc:subfield[@code='c']");
            if (!array_key_exists(0, $editorArrayBis)) {
                $editor = null;
            } else {
                $editor = $editorArrayBis[0]->__toString();
            }
        } else {
            $editor = $editorArray[0]->__toString();
        }

        return $editor;
    }

    /**
     * Collection for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $collection
     */
    private function getCollection($xml)
    {
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
     * Publication date for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $date
     */
    private function getDate($xml)
    {
        // Date : "//mxc:datafield[@tag='210']/mxc:subfield[@code='d']"
        // Date : "//mxc:datafield[@tag='214']/mxc:subfield[@code='d']"
        $dateArray = $xml->xpath("//mxc:datafield[@tag='210']/mxc:subfield[@code='d']");
        if (!array_key_exists(0, $dateArray)) {
            $dateArrayBis = $xml->xpath("//mxc:datafield[@tag='214']/mxc:subfield[@code='d']");
            if (!array_key_exists(0, $dateArrayBis)) {
                $date = null;
            } else {
                $date = $dateArrayBis[0]->__toString();
                $date = intval(preg_replace('/[^0-9]/', '', $date));
            }
        } else {
            $date = $dateArray[0]->__toString();
            $date = intval(preg_replace('/[^0-9]/', '', $date));
        }

        return $date;
    }

    /**
     * Price for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $price
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
     * Pages for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $pages
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
     * Cover URL for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $coverUrl
     */
    private function getArk($xml)
    {
        $arkArray = $xml->xpath("//srw:recordIdentifier");
        if (!array_key_exists(0, $arkArray)) {
            $coverUrl = null;
        } else {
            $ark = $arkArray[0]->__toString();
            $coverUrl = "https://catalogue.bnf.fr/couverture?&appName=NE&idArk={$ark}&couverture=1";
        }

        return $coverUrl;
    }

    /**
     * Summary for the given book
     * 
     * @param SimpleXMLElement $xml
     * 
     * @return string|null $summary
     */
    private function getSummary($xml)
    {
        $summaryArray = $xml->xpath("//mxc:datafield[@tag='330']/mxc:subfield[@code='a']");
        if (array_key_exists(0, $summaryArray)) {
            $summary = $summaryArray[0]->__toString();
        } else {
            $summaryArrayBis = $xml->xpath("//mxc:datafield[@tag='339']/mxc:subfield[@code='a']");
            if (!array_key_exists(0, $summaryArrayBis)) {
                $summary = null;
            } else {
                $summary = $summaryArrayBis[0]->__toString();
            }
        }

        return $summary;
    }
}
