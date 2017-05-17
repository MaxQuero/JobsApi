<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class JobController extends Controller
{
    public function indexAction()
    {

        $url ='https://emploi.alsacreations.com/';
        $content= file_get_contents($url);
      var_dump($content);
        // recherche l'ip:
        preg_match_all('#<span class="title-link">(.*?)</span>#', $content, $title);
        preg_match_all('#<b class="societe">(.*)</b>#', $content, $society);
        preg_match_all('#</i>(.*)<br>#', $content, $place);

        // var_dump($mon_ip);
        var_dump($place);
        die('ok');
        /*$mon_ip =$mon_ip[1];
        echo $mon_ip = $mon_ip[0];
        curl_close(@$ch);
?
        var_dump($html);*/

        return 1;
    }
}