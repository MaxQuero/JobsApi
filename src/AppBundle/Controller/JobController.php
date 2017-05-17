<?php

namespace AppBundle\Controller;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class JobController extends Controller
{
    public function indexAction()
    {

        $url ='https://emploi.alsacreations.com/';
        $content= file_get_contents($url);

        $html = $this->getElContentsByTagClass($content,'ul','annonces');
        /*echo '<pre>';
        print_r($html);
        echo '</pre>';*/
       $test = $this->getElContentsByTagClass($content, 'li', 'offre');
        foreach ($test as $te) {
            $title = $this->getElContentsByTagClass($te, 'span', 'title-link');
            $society = $this->getElContentsByTagClass($te, 'b', 'societe');
            $place = $this->getElContentsByTagClass($te, 'span', 'lieu');
            preg_match_all('#<i aria-hidden="true" class="icon icon-location"></i>(.*?)<br>#', $te, $town);
            preg_match_all('#<br>\((.*?)\)</span>#', $te, $department);

        }

        return 1;
    }

    function DOMinnerHTML(DOMNode $element)
    {
        $innerHTML = "";
        $children = $element->childNodes;
        foreach ($children as $child)
        {
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }
        return $innerHTML;
    }

    function getElContentsByTagClass($html,$tag,$class)
    {
        $doc = new DOMDocument();
        $doc->getElementsByTagName('*');

        libxml_use_internal_errors(true);
        $doc->loadHTML($html);//Turn the $html string into a DOM document
        libxml_use_internal_errors(false);
        $els = $doc->getElementsByTagName($tag); //Find the elements matching our tag name ("div" in this example)
        foreach($els as $el)
        {
            //for each element, get the class, and if it matches return it's contents
            $classAttr = $el->getAttribute("class");
            if(preg_match('#\b'.$class.'\b#',$classAttr) > 0) {
                $arr[] = $this->DOMinnerHTML($el);
            }
        }
        return $arr;

    }
}
