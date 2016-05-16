<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Picture;
use AppBundle\Form\PictureType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="list")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $objects = $this->get('aws.s3.manager')->getAllObject();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $objects,
            $request->query->getInt('page', 1), 20
        );
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.file.manager')->fileManager($picture);
            $em = $this->getDoctrine()->getManager();
            $em->persist($picture);
            $em->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render("AppBundle::list.html.twig", ['pagination' => $pagination, 'form' => $form->createView()]);
    }

    /**
     * @Route("/objects_show/{key}", name="object_show", requirements={"key"=".+"})
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function objectShowActionRequest($key)
    {
        $deleteForm = $this->createDeleteForm($key);
        return $this->render('@App/object.html.twig', array(
            'key' => $key,
            'form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param $key
     * @Route("/objects/{key}", name="delete", requirements={"key"=".+"})
     * @Method("DELETE")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $key)
    {
        $em = $this->getDoctrine()->getManager();
        $picture = $em->getRepository('AppBundle:Picture')->findOneBy(['pictureName' => $key]);
        $form = $this->createDeleteForm($key);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($picture);
            $em->flush();
            try {
                $this->get('aws.s3.manager')->removeObject($key);
            } catch (\Exception $e) {

                return $e->getMessage();
            }
        }

        return $this->redirectToRoute('list');
    }

    private function createDeleteForm($key)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delete', array('key' => $key)))
            ->setMethod('DELETE')
            ->getForm();
    }
}
