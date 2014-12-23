<?php

namespace Kodify\BlogBundle\Tests\Controller;

use Kodify\BlogBundle\Entity\Author;
use Kodify\BlogBundle\Entity\Comment;
use Kodify\BlogBundle\Entity\Post;
use Kodify\BlogBundle\Tests\BaseFunctionalTest;

class CommentControllerTest extends BaseFunctionalTest
{
    public function testAddCommentsToPost()
    {
        $idPost = $this->createComments(3);
        $crawler = $this->client->request('GET', '/posts/' . $idPost);
        $this->assertTextFound($crawler, "Comments");
        $this->assertTextFound($crawler, 'comment 0');
        $this->assertTextFound($crawler, 'comment 1');
        $this->assertTextFound($crawler, 'comment 2');
    }

    protected function createComments($count)
    {
        $author = new Author();
        $author->setName('Author');
        $this->entityManager()->persist($author);
        $this->entityManager()->flush();
        $post = new Post();
        $post->setTitle('Title');
        $post->setContent('Content');
        $post->setAuthor($author);
        $this->entityManager()->persist($post);

        for($i=0; $i < $count; $i++)
        {
            $comment = new Comment();
            $comment->setAuthor($author)
                ->setComment('comment ' . $i)
                ->setPost($post)
            ;
            $this->entityManager()->persist($comment);
        }

        $this->entityManager()->flush();

        return $post->getId();
    }
}
