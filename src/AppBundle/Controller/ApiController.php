<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Picture;
use AppBundle\Form\PictureType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends FOSRestController
{
    /**
     * retrieve all pictures
     * TODO: add pagination
     *
     * @return Picture[]
     */
    public function getPicturesAction()
    {
        $pictures = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Picture')
            ->findAll();

        return $pictures;
    }

    /**
     * it generates so the route GET api/pictures/{id}
     *
     * @return Picture
     */
    public function getPictureAction(Picture $picture)
    {
        return $picture;
    }

    public function postPicturesAction(Request $request)
    {
        $picture = new Picture();
        $errors = $this->treatAndValidateRequest($picture, $request);
        if (count($errors) > 0) {
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        $this->persistAndFlush($picture);

        return new View($picture, Response::HTTP_CREATED);
    }

    public function putPictureAction(Picture $picture, Request $request)
    {
        $id = $picture->getId();
        $picture = new Picture();
        $picture->setId($id);
        $errors = $this->treatAndValidateRequest($picture, $request);
        if (count($errors) > 0) {
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        $this->persistAndFlush($picture);

        return "";
    }
    /**
     * @return array
     */
    private function treatAndValidateRequest(Picture $picture, Request $request)
    {
        $form = $this->createForm(
            new PictureType(),
            $picture,
            array(
                'method' => $request->getMethod()
            )
        );
        $form->handleRequest($request);
        $errors = $this->get('validator')->validate($picture);

        return $errors;
    }
    private function persistAndFlush(Picture $picture)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($picture);
        $manager->flush();
    }
}