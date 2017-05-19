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
     * Gives all jobs with json format
     *
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

        // Getting the view handler
        $viewHandler = $this->get('fos_rest.view_handler');

        // Creation of a view FOSUserBundle
        $view = View::create($formatted);
        $view->setFormat('json');

        //Handle the response
        return $viewHandler->handle($view);
    }

    /**
     * Scrap alsaCreation website and parse all last jobs data into db
     *
     * @Get("/jobs")
     */
    public function getInfosAction()
    {

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $response =

        error_reporting(~0);
        ini_set('display_errors', 1);
        $url = 'https://emploi.alsacreations.com/';
        $content = file_get_contents($url, false, stream_context_create($arrContextOptions));;

        // truncate Table so that we have only recent ones
        $this->truncateAction();

        $offres = $this->getElContentsByTagClass($content, 'li', 'offre');

        //var_dump($offres);
        foreach ($offres as $offre) {
            $title = $this->getElContentsByTagClass($offre, 'span', 'title-link');
            $title = utf8_decode(array_shift($title));
            $society = $this->getElContentsByTagClass($offre, 'b', 'societe');
            $society = utf8_decode(array_shift($society));
            //var_dump(utf8_decode($title));
            //$place = $this->getElContentsByTagClass($te, 'span', 'lieu');
            preg_match_all('#<i aria-hidden="true" class="icon icon-location"></i>(.*?)<#', $offre, $town);
            $city = array_shift($town[1]);
            preg_match_all('#<br>\((.*?)\)</span>#', $offre, $department);
            $department = array_shift($department[1]);

            // Save into DB
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

    /**
     * Get content of tag + classs name
     */
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
     * Create a new job
     *
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

    /**
     * Truncate job table and set auto_increment id to 1
     *
     */
    function truncateAction()
    {
        $em = $this->getDoctrine()->getManager();
        $className = 'AppBundle:Jobs';
        $cmd = $em->getClassMetadata($className);
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');

    }
}
