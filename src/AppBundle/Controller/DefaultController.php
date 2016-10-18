<?php

namespace AppBundle\Controller;

use Circle\RestClientBundle\Exceptions\OperationTimedOutException;
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
        $restClient = $this->container->get('circle.restclient');

        try {
            $response = $restClient->get('http://pizzapi.herokuapp.com/pizzas');
            $pizzas = json_decode($response->getContent(), true);
            return $this->render('default/index.html.twig', ['pizzas' => $pizzas]);
        } catch (OperationTimedOutException $exception) {
            return new Response("API indisponible");
        }
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
        $restClient = $this->container->get('circle.restclient');

        try {
            $restClient->post('http://pizzapi.herokuapp.com/orders', json_encode($data, JSON_NUMERIC_CHECK));
            return $this->redirectToRoute('homepage');
        } catch (OperationTimedOutException $exception) {
            return new Response("API indisponible");
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/admin", name="admin")
     */
    public function adminAction(Request $request)
    {
        $restClient = $this->container->get('circle.restclient');

        try {
            $response = $restClient->get('http://pizzapi.herokuapp.com/orders');
            $orders = json_decode($response->getContent(), true);
            return $this->render('default/admin.html.twig', ['orders' => $orders]);
        } catch (OperationTimedOutException $exception) {
            return new Response("API indisponible");
        }
    }
}
