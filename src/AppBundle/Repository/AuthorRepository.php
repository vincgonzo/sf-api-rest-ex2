<?php

namespace AppBundle\Repository;

class AuthorRepository extends AbstractRepository
{
    public function search($term, $order = 'asc', $limit = 2, $offset = 0)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.fullname', $order)
        ;

        if ($term) {
            $qb
                ->where('a.fullname LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }

        /*if($offset != 0)
        {
            $qb
                ->where('a.id >= ?1')
                ->setParameter(1, $limit * $offset)
            ;
        }*/

        //dump($qb->getQuery());die();

        return $this->paginate($qb, $limit, $offset);
    }
}