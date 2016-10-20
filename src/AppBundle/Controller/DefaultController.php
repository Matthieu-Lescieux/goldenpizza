<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use CacheBundle\Entity\Error;
use Circle\RestClientBundle\Exceptions\OperationTimedOutException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('cache');
        $restClient = $this->container->get('circle.restclient');
        $lastErrors = [];

        try {
            $lastErrors = $this->getDoctrine()->getManager('cache')->getRepository('CacheBundle:Error')->findLastMinuteForType('/pizzas');
        } catch (\Exception $exception) {
          //  dump("Can't load last errors from cache");
        }

        if (count($lastErrors) <= 1) {
            try {
                $response = $restClient->get('http://pizzapi.herokuapp.com/pizzas');

                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 500) {
                    $persistedResponse = $em->getRepository('CacheBundle:Response')->findOneBy(['type' => '/pizzas']);

                    if ($persistedResponse === null) {
                        $persistedResponse = new \CacheBundle\Entity\Response();
                        $persistedResponse->setType('/pizzas');
                    }

                    $persistedResponse->setContent($response->getContent());
                    $em->persist($persistedResponse);
                    $em->flush();
                } else {
                    throw new HttpException($response->getStatusCode());
                }

                $pizzas = json_decode($response->getContent(), true);

               // dump("Loaded from API");
                return $this->render('default/index.html.twig', ['pizzas' => $pizzas]);
            } catch (\Exception $exception) {
                try {
                    $error = new Error();
                    $error->setCreatedat(new \DateTime());
                    $error->setType('/pizzas');
                    $em->persist($error);
                    $em->flush();
                } catch(\Exception $exception) {

                }
            }
        }

        $pizzas = [];
        try {
            $lastResponse = $em->getRepository('CacheBundle:Response')->findOneBy(['type' => '/pizzas']);
            if (null !== $lastResponse) {
                $pizzasJson = $lastResponse->getContent();
                $pizzas = json_decode(trim($pizzasJson), true);
               // dump("Loaded from cache");
            } else {
                dump("Can't load history from cache");
            }
        } catch (\Exception $esception) {
            //dump("Can't load history from cache");
        }

        return $this->render('default/index.html.twig', ['pizzas' => $pizzas]);
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        $em = $this->getDoctrine()->getManager('cache');
        $restClient = $this->container->get('circle.restclient');
        $lastErrors = [];

        try {
            $lastErrors = $this->getDoctrine()->getManager('cache')->getRepository('CacheBundle:Error')->findLastMinuteForType('/orders');
        } catch (\Exception $exception) {
            dump("Can't load last errors from cache");
        }

        $message = new Message();
        $form = $this->createForm(new MessageType(), $message);

        if (count($lastErrors) <= 1) {
            try {
                $response = $restClient->get('http://pizzapi.herokuapp.com/orders');

                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 500) {
                    $persistedResponse = $em->getRepository('CacheBundle:Response')->findOneBy(['type' => '/orders']);

                    if ($persistedResponse === null) {
                        $persistedResponse = new \CacheBundle\Entity\Response();
                        $persistedResponse->setType('/orders');
                    }

                    $persistedResponse->setContent($response->getContent());
                    $em->persist($persistedResponse);
                    $em->flush();
                } else {
                    throw new HttpException($response->getStatusCode());
                }

                $orders = json_decode($response->getContent(), true);

                //dump("Loaded from API");
                return $this->render('default/admin.html.twig', ['orders' => $orders, 'form' => $form->createView()]);
            } catch (\Exception $exception) {
                try {
                    $error = new Error();
                    $error->setCreatedat(new \DateTime());
                    $error->setType('/orders');
                    $em->persist($error);
                    $em->flush();
                } catch(\Exception $exception) {

                }
            }
        }

        $orders = [];
        try {
            $lastResponse = $em->getRepository('CacheBundle:Response')->findOneBy(['type' => '/orders']);
            if (null !== $lastResponse) {
                $ordersJson = $lastResponse->getContent();
                $orders = json_decode(trim($ordersJson), true);
              //  dump("Loaded from cache");
            } else {
               // dump("Can't load history from cache");
            }
        } catch (\Exception $esception) {
           // dump("Can't load history from cache");
        }

        return $this->render('default/admin.html.twig', ['orders' => $orders, 'form' => $form->createView()]);
    }
}
