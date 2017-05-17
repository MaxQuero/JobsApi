<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Jobs;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View; // Utilisation de la vue de FOSRestBundle
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    /**
     * @Get("/api/jobs")
     */
    public function indexAction()
    {

        $jobs = $this->getDoctrine()
            ->getRepository('AppBundle:Jobs')
            ->findAll();

        if (!$jobs) {
            throw $this->createNotFoundException(
                'No jobs found'
            );
        }

        $formatted = [];
        foreach ($jobs as $job) {
            $formatted[] = [
                'id' => $job->getId(),
                'title' => $job->getTitle(),
                'society' => $job->getSociety(),
                'city' => $job->getCity(),
                'department' => $job->getDepartment()
            ];
        }

        // Récupération du view handler
        $viewHandler = $this->get('fos_rest.view_handler');

        // Création d'une vue FOSRestBundle
        $view = View::create($formatted);
        $view->setFormat('json');

        // Gestion de la réponse
        return $viewHandler->handle($view);
    }

    /**
     * @Get("/jobs")
     */
    public function getInfosAction()
    {
        $url = 'https://emploi.alsacreations.com/';
        $content = file_get_contents($url);

        $this->deleteAction();
        $test = $this->getElContentsByTagClass($content, 'li', 'offre');
        foreach ($test as $te) {
            $title = $this->getElContentsByTagClass($te, 'span', 'title-link');
            $title = array_shift($title);
            $society = $this->getElContentsByTagClass($te, 'b', 'societe');
            $society = array_shift($society);
            //$place = $this->getElContentsByTagClass($te, 'span', 'lieu');
            preg_match_all('#<i aria-hidden="true" class="icon icon-location"></i>(.*?)<br>#', $te, $town);
            $city = array_shift($town[1]);
            preg_match_all('#<br>\((.*?)\)</span>#', $te, $department);
            $department = array_shift($department[1]);
            //destroy pour recréer
            $this->createAction($title, $society, $city, $department);


        }
        return new Response('New jobs reloaded');
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

    /**
     * @Post("/job")
     */
    function createAction($title, $society, $city, $department){
        $job = New Jobs();
        $job->setTitle($title);
        $job->setSociety($society);
        $job->setcity($city);
        $job->setDepartment($department);
        $em = $this->getDoctrine()->getManager();

        $em->persist($job);

        $em->flush();

        return new Response('Saved new job with id '.$job->getId());
    }

    function deleteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $jobs = $this->getDoctrine()
            ->getRepository('AppBundle:Jobs')
            ->findAll();
        foreach ($jobs as $job) {
            $em->remove($job);
            $em->flush();
        }

    }
}
