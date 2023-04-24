<?php

namespace App\Controller\Api;

use App\Service\ApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiBnfController extends AbstractController
{
    /**
     * @Route("/booksByIsbn", name="app_api")
     */
    public function bookByIsbn(ApiManager $apiManager, SerializerInterface $serializer): Response
    {


    $isbn = $this->getISBN($xml);
    $title = $this->getTitle($xml);
    $author = $this->getAuthor($xml);
    $editor = $this->getEditor($xml);
    $collection = $this->getCollection($xml);
    $date = $this->getDate($xml);
    $price = $this->getPrice($xml);
    $ark = $this->getArk($xml);
    $summary = $this->getSummary($xml);

}
    private function getISBN($xml)
    {
         // ISBN : "//mxc:datafield[@tag='073']/mxc:subfield[@code='a']"
         $isbnArray = $xml->xpath("//mxc:datafield[@tag='073']/mxc:subfield[@code='a']");
         // Does the isbn exist ?
         if (!array_key_exists(0, $isbnArray)) {
             echo "ISBN : NULL <br/>";
         } else {
             $isbn = $isbnArray[0]->__toString();
             echo "ISBN : " . $isbn . "<br/>";
         }
    }

    private function getTitle($xml)
    {
        // Title: "//mxc:datafield[@tag='200']/mxc:subfield[@code='a']"
        $titleArray = $xml->xpath("//mxc:datafield[@tag='200']/mxc:subfield[@code='a']");

        if (!array_key_exists(0, $titleArray)) {
            echo "Titre : NULL <br/>";
        } else {
            $title = $titleArray[0]->__toString();
            echo "Titre : " . $title . "<br/>";
        }

    }

    private function getAuthor($xml)
    {
        // Author lastname : "//mxc:datafield[@tag='700']/mxc:subfield[@code='a']"
        // Author firstname : "//mxc:datafield[@tag='700']/mxc:subfield[@code='b']"
        // Author2 lastname : "//mxc:datafield[@tag='701']/mxc:subfield[@code='a']"
        // Author2 firstname : "//mxc:datafield[@tag='701']/mxc:subfield[@code='b']"
        $authorLastnameArray = $xml->xpath("//mxc:datafield[@tag='700']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $authorLastnameArray)) {
            echo "Nom auteur : NULL <br/>";
        } else {
            $authorLastname = $authorLastnameArray[0]->__toString();
            echo "Nom auteur : " . $authorLastname . "<br/>";
        }

        $authorFirstnameArray = $xml->xpath("//mxc:datafield[@tag='700']/mxc:subfield[@code='b']");
        if (!array_key_exists(0, $authorFirstnameArray)) {
            echo "Prénom auteur : NULL <br/>";
        } else {
            $authorFirstname = $authorFirstnameArray[0]->__toString();
            echo "Prénom auteur : " . $authorFirstname . "<br/>";
        }

    }

    private function getEditor($xml)
    {
        // Editor : "//mxc:datafield[@tag='210']/mxc:subfield[@code='c']"
        // Editor : "//mxc:datafield[@tag='214']/mxc:subfield[@code='c']" + [ind2 ='0']
        $editorArray = $xml->xpath("//mxc:datafield[@tag='210']/mxc:subfield[@code='c']");
        if (!array_key_exists(0, $editorArray)) {
            $editorArrayBis = $xml->xpath("//mxc:datafield[@tag='214']/mxc:subfield[@code='c']");
            if (!array_key_exists(0, $editorArrayBis)) {
                echo "Editeur : : NULL <br/>";
            } else {
                $editor = $editorArrayBis[0]->__toString();
                echo "Editeur : " . $editor . "<br/>";
                }
        } else {
            $editor = $editorArray[0]->__toString();
            echo "Editeur : " . $editor . "<br/>";
        }
    }

    private function getCollection($xml)
    {
        // Collection : "//mxc:datafield[@tag='225']/mxc:subfield[@code='a']"
        // Collection : "//mxc:datafield[@tag='225']/mxc:subfield[@code='i']"
        $collectionArray = $xml->xpath("//mxc:datafield[@tag='225']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $collectionArray)) {
            $collectionArrayBis = $xml->xpath("//mxc:datafield[@tag='225']/mxc:subfield[@code='i']");
            if (!array_key_exists(0, $collectionArrayBis)) {
                echo "Collection : NULL <br/>";
            } else{
                $collection = $collectionArrayBis[0]->__toString();
                echo "Collection : " . $collection . "<br/>";
            }
            
        } else {
            $collection = $collectionArray[0]->__toString();
            echo "Collection : " . $collection . "<br/>";
        }
    }


    private function getDate($xml)
    {
        // Date : "//mxc:datafield[@tag='210']/mxc:subfield[@code='d']"
        // Date : "//mxc:datafield[@tag='214']/mxc:subfield[@code='d']"
        $dateArray = $xml->xpath("//mxc:datafield[@tag='210']/mxc:subfield[@code='d']");
        if (!array_key_exists(0, $dateArray)) {
            $dateArrayBis = $xml->xpath("//mxc:datafield[@tag='214']/mxc:subfield[@code='d']");
            if (!array_key_exists(0, $dateArrayBis)) {
                echo "Date : NULL <br/>";
            } else {
                $date = $dateArrayBis[0]->__toString();
                echo "Date : " . $date . "<br/>";
                }
        } else {
            $date = $dateArray[0]->__toString();
            echo "Date : " . $date . "<br/>";
        }
    }

    private function getPrice()
    {
         // Price : "//mxc:datafield[@tag='010']/mxc:subfield[@code='d']"
         $priceArray = $xml->xpath("//mxc:datafield[@tag='010']/mxc:subfield[@code='d']");
         if (!array_key_exists(0, $priceArray)) {
             echo "Prix : NULL <br/>";
         } else {
             $price = $priceArray[0]->__toString();
             echo "Prix : " . $price . "<br/>";
         }
    }

    private function getArk($xml)
    {
        // Ark : "//srw:recordIdentifier"
        $arkArray = $xml->xpath("//srw:recordIdentifier");
        if (!array_key_exists(0, $arkArray)) {
            echo "Ark : NULL <br/>";
        } else {
            $ark = $arkArray[0]->__toString();
            $coverUrl = "https://catalogue.bnf.fr/couverture?&appName=NE&idArk={$ark}&couverture=1";
            echo "Ark : {$ark}<br/>";
            echo "Couverture : {$coverUrl} <br/>";
        }
    }

    private function getSummary($xml)
    {
       // Summary : "//mxc:datafield[@tag='330']/mxc:subfield[@code='a']"
        // Summary : "//mxc:datafield[@tag='339']/mxc:subfield[@code='a']"
        $summaryArray = $xml->xpath("//mxc:datafield[@tag='330']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $summaryArray)) {
            $summaryArrayBis = $xml->xpath("//mxc:datafield[@tag='339']/mxc:subfield[@code='a']");
            if (!array_key_exists(0, $summaryArrayBis)) {
                echo "Résumé : NULL <br/>";
            } else {
                $summary = $summaryArrayBis[0]->__toString();
                echo "Résumé : " . $summary . "<br/>";
                }
        } else {
            $summary = $summaryArray[0]->__toString();
            echo "Résumé : " . $summary . "<br/>";
        }

        return $this->render('api/api_bnf/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

}
