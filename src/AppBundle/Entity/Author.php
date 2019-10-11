<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthorRepository")
 * @ORM\Table()
 *
 * @Serializer\ExclusionPolicy("ALL")
 */
class Author
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    private $fullname;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    private $biography;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="author", cascade={"persist"})
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getBiography()
    {
        return $this->biography;
    }

    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    public function getArticles()
    {
        return $this->articles;
    }
}