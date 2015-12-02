<?php

namespace AppBundle\Presenters;

class CommentRatingPresenter {
    public function present($data) {
        return array(
            'count' => $data['rating']['voteCount'],
            'rating' => $data['rating']['total']
        );
    }
}
