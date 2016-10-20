<?php

namespace ApimockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/apimock/pizzas")
     */
    public function pizzaAction()
    {
        $pizzas = [
            [
                'id' => 1,
                'name' => 'margarita',
                'price' => 1240
            ],
            [
                'id' => 2,
                'name' => 'regina',
                'price' => 1320
            ]
        ];

        $delay = $this->container->getParameter('apimock_delay');
        sleep($delay);

        return new JsonResponse($pizzas);
    }

    /**
     * @Route("/apimock/orders")
     */
    public function orderAction()
    {
        $orders = [
            [
                'id' => 1,
                'pizza' => [
                    'id' => 1,
                    'name' => 'margarita',
                    'price' => 1240
                ],
                'status' => 'COOKING'
            ],
            [
                'id' => 2,
                'pizza' => [
                    'id' => 2,
                    'name' => 'regina',
                    'price' => 1320
                ],
                'status' => 'COOKING'
            ]
        ];

        $delay = $this->container->getParameter('apimock_delay');
        sleep($delay);

        return new JsonResponse($orders);
    }
}
