<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Exception\ResourceValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends FOSRestController
{
     /**
     * @Rest\Get("/authors", name="app_authors_list")
     * @Rest\View(statusCode = 201)
     */
    public function listAction()
    {
        $authors = $this->getDoctrine()->getRepository('AppBundle:Author')-findAll();

        return $authors;
    }
    
   /* public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('AppBundle:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Articles($pager);
    }
    */
    
    /**
     * @Rest\Get(
     *     path = "/author/{id}",
     *     name = "app_author_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     */
    public function showAction(Author $author)
    {
        return $author;
    }

    /**
     * @Rest\Post("/author")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("author", converter="fos_rest.request_body")
     */
    public function createAction(Author $author)
    {
        $em = $this->getDoctrine()->getManager();

        $em->persist($author);
        $em->flush();

        return $author;
    }
}
