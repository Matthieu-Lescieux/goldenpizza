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
        $restClient = $this->container->get('circle.restclient');

        try {
            $response = $restClient->get('http://pizzapi.herokuapp.com/pizzas');

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 500) {
                $em = $this->getDoctrine()->getManager('cache');
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

            return $this->render('default/index.html.twig', ['pizzas' => $pizzas]);
        } catch (\Exception $exception) {
            $error = new Error();
            $error->setCreatedat(new \DateTime());
            $error->setType('/pizzas');
            $em = $this->getDoctrine()->getManager('cache');
            $em->persist($error);
            $em->flush();

            $pizzasJson = $em->getRepository('CacheBundle:Response')->findOneBy(['type' => '/pizzas'])->getContent();
            $pizzas = json_decode(trim($pizzasJson), true);

            dump($pizzas);

            return $this->render('default/index.html.twig', ['pizzas' => $pizzas]);
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
    public function adminAction()
    {
        $restClient = $this->container->get('circle.restclient');
        $message = new Message();
        $form = $this->createForm(new MessageType(), $message);

        try {
            $response = $restClient->get('http://pizzapi.herokuapp.com/orders');
            $orders = json_decode($response->getContent(), true);

            return $this->render('default/admin.html.twig', ['orders' => $orders, 'form' => $form->createView()]);
        } catch (OperationTimedOutException $exception) {
            return $this->render('default/admin.html.twig', ['orders' => [], 'form' => $form->createView()]);
        }
    }
}
