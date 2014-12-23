<?php

namespace Kodify\BlogBundle\Controller;

use Kodify\BlogBundle\Entity\Comment;
use Kodify\BlogBundle\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kodify\BlogBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    public function addAction(Request $request, $post_id)
    {
        $post = $this->getDoctrine()->getManager()->getRepository('KodifyBlogBundle:Post')->find($post_id);
        if (!$post instanceof Post)
        {
            throw $this->createNotFoundException('Post not found');
        }

        $comment = new Comment();
        $comment->setPost($post);

        $form = $this->createForm(
            new CommentType(),
            $comment
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment = $form->getData();
            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirect($this->generateUrl('view_post',array('id' => $post_id)));
    }
}
