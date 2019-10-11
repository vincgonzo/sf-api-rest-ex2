<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Exception\ResourceValidationException;
use AppBundle\Representation\Articles;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends FOSRestController
{
    /**
     * @Rest\Get("/articles", name="app_article_list")
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
     *     default="15",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        
        $pager = $this->getDoctrine()->getRepository('AppBundle:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Articles($pager);
    }

    /**
     * @Rest\Get(
     *     path = "/article/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     */
    public function showAction(Article $article)
    {
        return $article;
    }

    /**
     * @Rest\Post(
     *     "/article/update/{post_id}",
     *     name = "app_article_update",
     *     requirements={"post_id" = "\d+"}
     * )
     * @ParamConverter("article", converter="fos_rest.request_body")
     * @Rest\View(statusCode=201)
     */
    public function updateAction($post_id, Article $article, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            
            throw new ResourceValidationException($message);
        }
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:Article')->find($post_id);
        $repo->setTitle($article->getTitle());
        $repo->setContent($article->getContent());
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($repo);
        $em->flush();

        return $repo;
    }

    /**
     * @Rest\Post(
     *     "/article/delete/{post_id}",
     *     name = "app_article_delete",
     *     requirements={"post_id" = "\d+"}
     * )
     * @ParamConverter("article", converter="fos_rest.request_body")
     * @Rest\View(statusCode=201)
     */
    // This particular route would not be accessible without auth process
    public function deleteAction($post_id, Article $article)
    {        
        $repo = $this->getDoctrine()->getRepository('AppBundle:Article')->find($post_id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($repo);
        $em->flush();

        return;
    }

    /**
     * @Rest\Post("/article")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("article", converter="fos_rest.request_body")
     */
    public function createAction(Article $article, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($article);
        $em->flush();

        return $article;
    }
}
