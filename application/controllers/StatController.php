<?php

class StatController extends PsController {

    public function indexPartial() {
    }

    public function tablePartial() {
    }

    public function fetchAction() {
        $id = (int)$this->getArgs()[0];

        $limits = $this->getArgs()[1];
        if (!empty($limits)) {
            $limits = explode('-', $limits);
            $stat = StatMapper::getInstance()->fetch([
                'link_id' => $id,
            ], (int)$limits[0] . ', ' . ((int)$limits[1] - (int)$limits[0]));
        } else {
            $stat = StatMapper::getInstance()->fetch([
                'link_id' => $id,
            ]);
        }

        $this->view->json([
            'items' => $stat,
            'count' => StatMapper::getInstance()->fetchCount([
                'link_id' => $id,
            ]),
        ]);
    }

    public function fetchByGroupAction() {
        $args = $this->getArgs();
        $countArgs = count($args);

        if ($countArgs != 1 && $countArgs != 3) {
            throw new Exception('Неправильный запрос');
        }

        $groupId = (int)$args[0];

        if ($countArgs == 1) {
            $this->view->json([
                'items' => StatMapper::getInstance()->fetchByGroup($groupId)
            ]);
        } else {
            $this->view->json([
                'items' => StatMapper::getInstance()->fetchByGroup($groupId,
                                                                   new DateTime($args[1]),
                                                                   new DateTime($args[2]))
            ]);
        }
    }

    public function fetchByLinkAction() {
        $args = $this->getArgs();
        $countArgs = count($args);

        if ($countArgs != 1 && $countArgs != 2) {
            throw new Exception('Неправильный запрос');
        }

        $linkId = (int)$args[0];

        if ($countArgs == 1) {
            $this->view->json([
                'items' => StatMapper::getInstance()->fetchByLink($linkId)
            ]);
        } else {
            $period = explode('-', $args[1]);

            $from = new DateTime($period[0]);
            $to = new DateTime($period[1]);
            $to->add(new DateInterval('P1D'));

            $this->view->json([
                'items' => StatMapper::getInstance()->fetchByLink($linkId, $from, $to)
            ]);
        }
    }
}
