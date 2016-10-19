<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommunicationController extends Controller
{
    /**
     * @Route("/communication", name="communication")
     */
    public function showAction() {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Message');
        $messages = $repository->findAll();

        return $this->render('communication/show.html.twig', ['messages' => $messages]);
    }

    /**
     * @Route("/message", name="message")
     */
    public function postAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $message = new Message();
            $form = $this->createForm(new MessageType(), $message);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($message);
                $em->flush();
            }

            return $this->redirectToRoute('admin');
        }
    }
}