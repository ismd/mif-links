<?php

class LinksController extends PsController {

    public function addAction() {
        $serverUrl = $this->getHelper('Server')->url();
        $request = $this->getRequest();

        if (!$request->isPost()) {
            throw new Exception('Не POST-запрос');
        }

        $post = $request->getPost();
        $link = $post->link;

        if (strstr($link, '://') === false) {
            $link = 'http://' . $link;
        }

        if ($post->force != 'true') {
            $links = LinkMapper::getInstance()->fetch([
                'link' => $link,
            ]);

            if (count($links) > 0) {
                $this->view->json([
                    'result' => 'duplicate',
                    'links' => array_map(function($link) use($serverUrl) {
                        $link['short_link'] = $serverUrl . '/' . $link['short_link'];
                        return $link;
                    }, $links),
                ]);
            }
        }

        $result = LinkMapper::getInstance()->add($link);
        $result['shortLink'] = $serverUrl . '/' . $result['shortLink'];

        $this->view->json([
            'result' => 'ok',
            'info' => $result,
        ]);
    }

    public function listAction() {
        $serverUrl = $this->getHelper('Server')->url();

        $limits = $this->getArgs()[0];
        if (!empty($limits)) {
            $limits = explode('-', $limits);
            $links = LinkMapper::getInstance()->fetch([], $limits[0] . ', ' . $limits[1]);
        } else {
            $links = LinkMapper::getInstance()->fetch();
        }

        $this->view->json([
            'links' => array_map(function($link) use($serverUrl) {
                $link['short_link'] = $serverUrl . '/' . $link['short_link'];
                return $link;
            }, $links),
            'count' => LinkMapper::getInstance()->fetchLinksCount(),
        ]);
    }

    public function regenerateAction() {
        $this->view->json([
            'result' => 'ok',
            'shortLink' => $this->getHelper('Server')->url() . '/' . LinkMapper::getInstance()->regenerate($this->getArgs()[0]),
        ]);
    }
}
