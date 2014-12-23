<?php

namespace Kodify\BlogBundle\Controller;

use Kodify\BlogBundle\Entity\Comment;
use Kodify\BlogBundle\Entity\Post;
use Kodify\BlogBundle\Form\Type\CommentType;
use Kodify\BlogBundle\Form\Type\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PostsController extends Controller
{
    const LIMIT_OF_COMMENTS_PER_PAGE = 3;

    public function indexAction()
    {
        $posts      = $this->getDoctrine()->getRepository('KodifyBlogBundle:Post')->latest();
        $template   = 'KodifyBlogBundle:Post:List/empty.html.twig';
        $parameters = ['breadcrumbs' => ['home' => 'Home']];
        if (count($posts)) {
            $template            = 'KodifyBlogBundle:Post:List/index.html.twig';
            $parameters['posts'] = $posts;
        }

        return $this->render($template, $parameters);
    }

    public function viewAction(Request $request, $id)
    {
        $currentPost = $this->getDoctrine()->getRepository('KodifyBlogBundle:Post')->find($id);
        if (!$currentPost instanceof Post) {
            throw $this->createNotFoundException('Post not found');
        }

        $commentsPagination = $this->getPaginationForComments($request, $id);

        $parameters = [
            'breadcrumbs'           => ['home' => 'Home'],
            'comments_pagination'   => $commentsPagination,
            'post'                  => $currentPost,
            'form_comment'          => $this->createCommentForm($id)->createView(),
        ];

        return $this->render('KodifyBlogBundle::Post/view.html.twig', $parameters);
    }

    protected function getPaginationForComments(Request $request, $post_id)
    {
        $query = $this->getDoctrine()->getRepository('KodifyBlogBundle:Comment')->queryPostsComments($post_id);
        $paginator  = $this->get('knp_paginator');

        return $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            self::LIMIT_OF_COMMENTS_PER_PAGE
        );
    }

    protected function createCommentForm($post_id)
    {
        return $this->createForm(
            new CommentType(),
            new Comment(),
            [
                'action'        => $this->generateUrl('add_comment', array('post_id' => $post_id)),
                'method'        => 'POST'
            ]
        );
    }

    public function createAction(Request $request)
    {
        $form       = $this->createForm(
            new PostType(),
            new Post(),
            [
                'action' => $this->generateUrl('create_post'),
                'method' => 'POST',
            ]
        );
        $parameters = [
            'form'        => $form->createView(),
            'breadcrumbs' => ['home' => 'Home', 'create_post' => 'Create Post']
        ];

        $form->handleRequest($request);
        if ($form->isValid()) {
            $post = $form->getData();
            $this->getDoctrine()->getManager()->persist($post);
            $this->getDoctrine()->getManager()->flush();
            $parameters['message'] = 'Post Created!';
        }

        return $this->render('KodifyBlogBundle:Default:create.html.twig', $parameters);
    }
}
