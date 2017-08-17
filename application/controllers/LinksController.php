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
                        $link['short_link_full'] = $serverUrl . '/' . $link['short_link'];
                        return $link;
                    }, $links),
                ]);
            }
        }

        $result = LinkMapper::getInstance()->add($link);
        $result['shortLinkFull'] = $serverUrl . '/' . $result['shortLink'];

        $this->view->json([
            'result' => 'ok',
            'info' => $result,
        ]);
    }

    public function editAction() {
        $serverUrl = $this->getHelper('Server')->url();
        $request = $this->getRequest();

        if (!$request->isPost()) {
            throw new Exception('Не POST-запрос');
        }

        $post = $request->getPost();

        $links = LinkMapper::getInstance()->fetch([
            'short_link' => $post->shortLink,
        ], 1);

        if (count($links) > 0) {
            $this->view->json([
                'result' => 'duplicate',
            ]);
        }

        $result = LinkMapper::getInstance()->edit($post->id, $post->shortLink);
        $result['shortLinkFull'] = $serverUrl . '/' . $result['shortLink'];

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
            $links = LinkMapper::getInstance()->fetch([], (int)$limits[0] . ', ' . ((int)$limits[1] - (int)$limits[0]));
        } else {
            $links = LinkMapper::getInstance()->fetch();
        }

        $this->view->json([
            'links' => array_map(function($link) use($serverUrl) {
                $link['short_link_full'] = $serverUrl . '/' . $link['short_link'];
                return $link;
            }, $links),
            'count' => LinkMapper::getInstance()->fetchLinksCount(),
        ]);
    }

    public function regenerateAction() {
        $id = $this->getArgs()[0];
        $shortLink = LinkMapper::getInstance()->regenerate($id);

        $this->view->json([
            'result' => 'ok',
            'info' => [
                'id' => $id,
                'shortLink' => $shortLink,
                'shortLinkFull' => $this->getHelper('Server')->url() . '/' . $shortLink,
            ],
        ]);
    }

    public function searchAction() {
        $serverUrl = $this->getHelper('Server')->url();
        $post = $this->getRequest()->getPost();

        $links = LinkMapper::getInstance()->search($post->search);

        $this->view->json([
            'links' => array_map(function($link) use($serverUrl) {
                $link['short_link_full'] = $serverUrl . '/' . $link['short_link'];
                return $link;
            }, $links),
            'idSearchRequest' => $post->idSearchRequest,
        ]);
    }
}
