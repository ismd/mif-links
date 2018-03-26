<?php

class GroupsController extends PsController {

    public function indexPartial() {
    }

    public function tablePartial() {
    }

    public function infoPartial() {
    }

    public function infoTablePartial() {
    }

    public function addAction() {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            throw new Exception('Не POST-запрос');
        }

        $post = $request->getPost();

        $groups = GroupMapper::getInstance()->fetch([
            'g.title' => $post->title,
        ]);

        if (count($groups) > 0) {
            $this->view->json([
                'result' => 'duplicate',
                'groups' => $groups,
            ]);
        }

        $result = GroupMapper::getInstance()->add($post->title);

        $this->view->json([
            'result' => 'ok',
        ]);
    }

    public function editAction() {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            throw new Exception('Не POST-запрос');
        }

        $post = $request->getPost();

        $groups = GroupMapper::getInstance()->fetch([
            'g.title' => $post->title,
        ], 1);

        if (count($links) > 0) {
            $this->view->json([
                'result' => 'duplicate',
            ]);
        }

        $result = GroupMapper::getInstance()->edit($post->id, $post->title);

        $this->view->json([
            'result' => 'ok',
        ]);
    }

    public function listAction() {
        $limits = $this->getArgs()[0];
        if (!empty($limits)) {
            $limits = explode('-', $limits);
            $groups = GroupMapper::getInstance()->fetch([], (int)$limits[0] . ', ' . ((int)$limits[1] - (int)$limits[0]));
        } else {
            $groups = GroupMapper::getInstance()->fetch();
        }

        $this->view->json([
            'items' => $groups,
            'count' => GroupMapper::getInstance()->fetchCount(),
        ]);
    }

    public function searchAction() {
        $post = $this->getRequest()->getPost();

        $groups = GroupMapper::getInstance()->search($post->search);

        $this->view->json([
            'items' => $groups,
        ]);
    }

    public function getAction() {
        $idGroup = (int)$this->getArgs()[0];
        $groups = GroupMapper::getInstance()->fetch(['g.id' => $idGroup], 1);

        if (count($groups) > 0) {
            $this->view->json([
                'result' => 'ok',
                'group' => $groups[0],
            ]);
        } else {
            $this->view->json([
                'result' => 'not_found',
            ]);
        }
    }
}
