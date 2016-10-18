<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /*
        $client = $this->get('guzzle.client');
        $request = $client->createRequest('GET', 'pizzapi.herokuapp.com/pizzas', ['headers' => ['Authorization: goldenpizza']]);
        $response = $client->send($request);
        */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'pizzapi.herokuapp.com/pizzas');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: goldenpizza'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        $pizzas = json_decode($response);

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'pizzas' => $pizzas
        ));
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function orderAction(Request $request, $id)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'pizzapi.herokuapp.com/orders/'.$id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: goldenpizza'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
    }
}
