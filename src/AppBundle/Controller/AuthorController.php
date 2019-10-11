<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Exception\ResourceValidationException;
use AppBundle\Representation\Authors;
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
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="2",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View(statusCode = 201)
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        //$authors = $this->getDoctrine()->getRepository('AppBundle:Author')->findAll();
        $pager = $this->getDoctrine()->getRepository('AppBundle:Author')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Authors($pager);
    }
    
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
    public function createAction(Author $author, ConstraintViolationList $violations)
    {
        if(count($violations))
        {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
         //   return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($author);
        $em->flush();

        return $author;
    }
}
