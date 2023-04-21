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
     * @Route("/bookByCover", name="app_api_cover")
     */
    public function bookByCover(ApiManager $apiManager, SerializerInterface $serializer): Response
{
    $cover = $apiManager->fetchCover('978220300102X3026');
   dd($cover);
}
    /**
     * @Route("/booksByIsbn", name="app_api")
     */
    public function bookByIsbn(ApiManager $apiManager, SerializerInterface $serializer): Response
    {
        //utilisation de la methode du service
        // 9782412066881
        // 9782811205065 (pas résumé)
        // 9791028100919 (pas collection)
        // 9782840985983 (n'existe pas)
        $xmlElement = $apiManager->fetchByISBN('9791037501165');
        //$objJsonDocument = json_encode($xmlElement);
        //$arrOutput = json_decode($objJsonDocument, TRUE);
        dump($xmlElement);

        // ISBN : "//mxc:datafield[@tag='073']/mxc:subfield[@code='a']"
        $isbnArray = $xmlElement->xpath("//mxc:datafield[@tag='073']/mxc:subfield[@code='a']");
        // Does the isbn exist ?
        if (!array_key_exists(0, $isbnArray)) {
            echo "ISBN : NULL <br/>";
        } else {
            $isbn = $isbnArray[0]->__toString();
            echo "ISBN : " . $isbn . "<br/>";
        }

        // Title: "//mxc:datafield[@tag='200']/mxc:subfield[@code='a']"
        $titleArray = $xmlElement->xpath("//mxc:datafield[@tag='200']/mxc:subfield[@code='a']");

        if (!array_key_exists(0, $titleArray)) {
            echo "Titre : NULL <br/>";
        } else {
            $title = $titleArray[0]->__toString();
            echo "Titre : " . $title . "<br/>";
        }

        // Author lastname : "//mxc:datafield[@tag='700']/mxc:subfield[@code='a']"
        // Author firstname : "//mxc:datafield[@tag='700']/mxc:subfield[@code='b']"
        // Author2 lastname : "//mxc:datafield[@tag='701']/mxc:subfield[@code='a']"
        // Author2 firstname : "//mxc:datafield[@tag='701']/mxc:subfield[@code='b']"
        $authorLastnameArray = $xmlElement->xpath("//mxc:datafield[@tag='700']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $authorLastnameArray)) {
            echo "Nom auteur : NULL <br/>";
        } else {
            $authorLastname = $authorLastnameArray[0]->__toString();
            echo "Nom auteur : " . $authorLastname . "<br/>";
        }

        $authorFirstnameArray = $xmlElement->xpath("//mxc:datafield[@tag='700']/mxc:subfield[@code='b']");
        if (!array_key_exists(0, $authorFirstnameArray)) {
            echo "Prénom auteur : NULL <br/>";
        } else {
            $authorFirstname = $authorFirstnameArray[0]->__toString();
            echo "Prénom auteur : " . $authorFirstname . "<br/>";
        }

        // Editor : "//mxc:datafield[@tag='210']/mxc:subfield[@code='c']"
        // Editor : "//mxc:datafield[@tag='214']/mxc:subfield[@code='c']" + [ind2 ='0']
        $editorArray = $xmlElement->xpath("//mxc:datafield[@tag='210']/mxc:subfield[@code='c']");
        if (!array_key_exists(0, $editorArray)) {
            $editorArrayBis = $xmlElement->xpath("//mxc:datafield[@tag='214']/mxc:subfield[@code='c']");
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

        // Collection : "//mxc:datafield[@tag='225']/mxc:subfield[@code='a']"
        // Collection : "//mxc:datafield[@tag='225']/mxc:subfield[@code='i']"
        $collectionArray = $xmlElement->xpath("//mxc:datafield[@tag='225']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $collectionArray)) {
            $collectionArrayBis = $xmlElement->xpath("//mxc:datafield[@tag='225']/mxc:subfield[@code='i']");
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

        // Date : "//mxc:datafield[@tag='210']/mxc:subfield[@code='d']"
        // Date : "//mxc:datafield[@tag='214']/mxc:subfield[@code='d']"
        $dateArray = $xmlElement->xpath("//mxc:datafield[@tag='210']/mxc:subfield[@code='d']");
        if (!array_key_exists(0, $dateArray)) {
            $dateArrayBis = $xmlElement->xpath("//mxc:datafield[@tag='214']/mxc:subfield[@code='d']");
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

        // Price : "//mxc:datafield[@tag='010']/mxc:subfield[@code='d']"
        $priceArray = $xmlElement->xpath("//mxc:datafield[@tag='010']/mxc:subfield[@code='d']");
        if (!array_key_exists(0, $priceArray)) {
            echo "Prix : NULL <br/>";
        } else {
            $price = $priceArray[0]->__toString();
            echo "Prix : " . $price . "<br/>";
        }

        // Ark : "//srw:recordIdentifier"
        $arkArray = $xmlElement->xpath("//srw:recordIdentifier");
        if (!array_key_exists(0, $arkArray)) {
            echo "Ark : NULL <br/>";
        } else {
            $ark = $arkArray[0]->__toString();
            $coverUrl = "https://catalogue.bnf.fr/couverture?&appName=NE&idArk={$ark}&couverture=1";
            echo "Ark : {$ark}<br/>";
            echo "Couverture : {$coverUrl} <br/>";
        }

        // Summary : "//mxc:datafield[@tag='330']/mxc:subfield[@code='a']"
        // Summary : "//mxc:datafield[@tag='339']/mxc:subfield[@code='a']"
        $summaryArray = $xmlElement->xpath("//mxc:datafield[@tag='330']/mxc:subfield[@code='a']");
        if (!array_key_exists(0, $summaryArray)) {
            $summaryArrayBis = $xmlElement->xpath("//mxc:datafield[@tag='339']/mxc:subfield[@code='a']");
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
