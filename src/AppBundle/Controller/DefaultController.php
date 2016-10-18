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
     * @param $pizzaId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/order/{pizzaId}", requirements={"pizzaId" = "\d+"}, name="order")
     */
    public function orderAction(Request $request, $pizzaId)
    {
        $data = ['id' => $pizzaId];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'pizzapi.herokuapp.com/orders');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: goldenpizza'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_NUMERIC_CHECK));
        curl_exec($ch);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/admin", name="admin")
     */
    public function adminAction(Request $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'pizzapi.herokuapp.com/orders');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: goldenpizza'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        $orders = json_decode($response);

        // replace this example code with whatever you need
        return $this->render('default/admin.html.twig', array(
            'orders' => $orders
        ));
    }
}
