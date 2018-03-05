<?php

class GroupsController extends PsController {

    public function indexPartial() {
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

        $groups = GroupsMapper::getInstance()->fetch([
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
            'info' => $result,
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
            'groups' => $groups,
            'count' => GroupMapper::getInstance()->fetchCount(),
        ]);
    }

    public function searchAction() {
        $post = $this->getRequest()->getPost();

        $groups = GroupMapper::getInstance()->search($post->search);

        $this->view->json([
            'groups' => $groups,
            'idSearchRequest' => $post->idSearchRequest,
        ]);
    }
}
