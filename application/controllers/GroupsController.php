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

        if (count($groups) > 0) {
            $this->view->json([
                'result' => 'duplicate',
            ]);
        }

        $result = GroupMapper::getInstance()->edit($post->id, $post->title);

        $this->view->json([
            'result' => 'ok',
        ]);
    }

    public function removeAction() {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            throw new Exception('Не POST-запрос');
        }

        $post = $request->getPost();
        $groupId = (int)$post->id;

        $links = LinkMapper::getInstance()->fetch([
            'g.id' => $groupId,
        ], 1);

        if (count($links) > 0) {
            $this->view->json([
                'result' => 'error',
                'text' => 'Можно удалять только группы без ссылок',
            ]);
        }

        if (GroupMapper::getInstance()->remove($groupId)) {
            $this->view->json([
                'result' => 'ok',
            ]);
        } else {
            $this->view->json([
                'result' => 'error',
                'text' => 'Не удалось удалить группу',
            ]);
        }
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
        $args = $this->getArgs();

        $idGroup = (int)$args[0];
        $countArgs = count($args);

        if ($countArgs == 2) {
            $period = explode('-', $args[1]);

            $from = new DateTime($period[0]);
            $to = new DateTime($period[1]);
            $to->add(new DateInterval('P1D'));
        } else {
            $from = null;
            $to = null;
        }

        $groups = GroupMapper::getInstance()->fetch(['g.id' => $idGroup], 1, $from, $to);

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
