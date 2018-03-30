<?php

class LinksController extends PsController {

    public function indexPartial() {
    }

    public function tablePartial() {
    }

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
                'l.link' => $link,
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

        $result = LinkMapper::getInstance()->add($link, $post->groupId);
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
            'l.short_link' => $post->shortLink,
        ], 1);

        $countLinks = count($links);
        if ($countLinks == 1 && $links[0]['id'] != $post->id || $countLinks > 1) {
            $this->view->json([
                'result' => 'duplicate',
            ]);
        }

        $result = LinkMapper::getInstance()->edit($post->id, $post->shortLink, $post->groupId);
        $result['shortLinkFull'] = $serverUrl . '/' . $result['shortLink'];

        $this->view->json([
            'result' => 'ok',
            'info' => $result,
        ]);
    }

    public function listAction() {
        $serverUrl = $this->getHelper('Server')->url();

        $args = $this->getArgs();
        $countArgs = count($args);

        if ($countArgs > 1) {
            $where = [
                'group_id' => (int)$args[1],
            ];
        } else {
            $where = [];
        }

        if ($countArgs == 3) {
            $period = explode('-', $args[2]);

            $from = new DateTime($period[0]);
            $to = new DateTime($period[1]);
            $to->add(new DateInterval('P1D'));
        } else {
            $period = null;
            $from = null;
            $to = null;
        }

        if ($countArgs > 0) {
            $limits = $args[0];
            $limits = explode('-', $limits);

            $links = LinkMapper::getInstance()->fetch($where, (int)$limits[0] . ', ' . ((int)$limits[1] - (int)$limits[0]), $from, $to);
        } else {
            $links = LinkMapper::getInstance()->fetch();
        }

        $this->view->json([
            'items' => array_map(function($link) use($serverUrl) {
                $link['short_link_full'] = $serverUrl . '/' . $link['short_link'];
                return $link;
            }, $links),
            'count' => LinkMapper::getInstance()->fetchCount($where),
        ]);
    }

    public function regenerateAction() {
        $id = $this->getArgs()[0];

        $links = LinkMapper::getInstance()->fetch([
            'l.id' => $id,
        ]);

        if (count($links) == 0) {
            $this->view->json([
                'result' => 'error',
            ]);
        }

        $link = $links[0];
        $shortLink = LinkMapper::getInstance()->regenerate($id);

        $this->view->json([
            'result' => 'ok',
            'info' => [
                'id' => $id,
                'shortLink' => $shortLink,
                'shortLinkFull' => $this->getHelper('Server')->url() . '/' . $shortLink,
                'groupId' => $link['group_id'],
            ],
        ]);
    }

    public function searchAction() {
        $serverUrl = $this->getHelper('Server')->url();
        $post = $this->getRequest()->getPost();

        if (!empty($post->groupId)) {
            $groupId = (int)$post->groupId;
        } else {
            $groupId = null;
        }

        $links = LinkMapper::getInstance()->search($post->search, $groupId);

        $this->view->json([
            'items' => array_map(function($link) use($serverUrl) {
                $link['short_link_full'] = $serverUrl . '/' . $link['short_link'];
                return $link;
            }, $links),
        ]);
    }

    public function getAction() {
        $idLink = (int)$this->getArgs()[0];
        $links = LinkMapper::getInstance()->fetch(['l.id' => $idLink], 1);

        if (count($links) > 0) {
            $link = $links[0];

            $serverUrl = $this->getHelper('Server')->url();
            $link['short_link_full'] = $serverUrl . '/' . $link['short_link'];

            $this->view->json([
                'result' => 'ok',
                'link' => $link,
            ]);
        } else {
            $this->view->json([
                'result' => 'not_found',
            ]);
        }
    }
}
