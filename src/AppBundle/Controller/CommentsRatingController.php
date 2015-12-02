<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Presenters;

class CommentsRatingController extends Controller
{
    public function fetchAction(Request $request)
    {
        $id = $request->query->get('comment_id');
        $comment = $this->getCommentById($id);

        $ratingRepo = $this->getDoctrine()
            ->getRepository('AppBundle:CommentRating');

        $rating = $ratingRepo->getRatingForComment($id);

        $presenter = new Presenters\CommentRatingPresenter();
        $data = $presenter->present(array(
            'rating' => $rating[0]
        ));

        $output = $this->presentResponse($data, 'comment_rating', array(
            'comment_id' => $id
        ));

        return new JsonResponse($output);
    }

    public function rateAction(Request $request) {
        // Retreive and normalise query parameters
        // Epic security here, this would obviously be the current logged in user's ID
        $user_id = $request->query->get('user_id');
        $id      = $request->query->get('comment_id');
        $vote    = $request->query->get('vote');

        switch ($vote) {
            case 'up':
                $value = 1;
                break;
            case 'down':
                $value = -1;
                break;
            default:
                $value = false;
        }

        $makeResponse = function ($data, $statusCode=200) use ($user_id, $id, $vote) {
            $output = $this->presentResponse($data, 'comment_rating_add', array(
                'vote' => $vote,
                'user_id' => $user_id,
                'comment_id' => $id
            ));
            return new JsonResponse($output, $statusCode);
        };

        // Make sure the vote is valid
        if ($value === false) {
            return $makeResponse('Param "vote" invalid. Expected "up" or "down"',Response::HTTP_BAD_REQUEST);
        }

        // Make sure it's a valid comment we're voting on
        $comment = $this->getCommentById($id);

        // Make sure they haven't already voted on the comment
        $ratingRepo = $this->getDoctrine()
            ->getRepository('AppBundle:CommentRating');

        $rating = $ratingRepo->findBy(array('user_id' => $user_id));

        if (!!$rating) {
            return $makeResponse('User has already submitted rating', Response::HTTP_BAD_REQUEST);
        }

        try {
            // If we made it here, everything looks good
            $result = $ratingRepo->addNewVote(array(
                'comment_id' => $id,
                'value'     => $value,
                'user_id'    => $user_id
            ));
        } catch (Exception $e) {
            return $makeResponse('Failed to save vote', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $makeResponse('Vote Added');
    }

    private function presentResponse($output, $feedName, $params) {
        $presenter = new Presenters\BaseApiPresenter;
        return $presenter->present(array(
            'output' => $output,
            'timestamp' => time(),
            'feed' => $feedName,
            'params' => $params
        ));
    }

    private function getCommentById($id) {
        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:Comment');

        $comment = $repo->find($id);

        if (!$comment) {
            throw $this->createNotFoundException(
                'No comment found for id '.$id
            );
        }

        return $comment;
    }
}

