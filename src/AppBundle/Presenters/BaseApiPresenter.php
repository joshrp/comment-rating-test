<?php

namespace AppBundle\Presenters;

class BaseApiPresenter {

    public function present($data) {
        return array(
            'data' => $data['output'],
            'meta' => array (
                'feed' => $data['feed'],
                'generated_at' => date('c', $data['timestamp']),
                'params' => $data['params']
            )
        );
    }
}
