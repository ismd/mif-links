<?php

class StatController extends PsController {

    public function indexPartial() {
    }

    public function tablePartial() {
    }

    public function fetchAction() {
        $args = $this->getArgs();
        $id = (int)$args[0];

        $limits = $args[1];

        if (count($args) == 3) {
            $period = explode('-', $args[2]);

            $from = new DateTime($period[0]);
            $to = new DateTime($period[1]);
            $to->add(new DateInterval('P1D'));
        } else {
            $period = null;
            $from = null;
            $to = null;
        }

        if (!empty($limits)) {
            $limits = explode('-', $limits);
            $stat = StatMapper::getInstance()->fetch([
                'link_id' => $id,
            ], (int)$limits[0] . ', ' . ((int)$limits[1] - (int)$limits[0]), $from, $to);
        } else {
            $stat = StatMapper::getInstance()->fetch([
                'link_id' => $id,
            ], null, $from, $to);
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

        if ($countArgs != 1 && $countArgs != 2) {
            throw new Exception('Неправильный запрос');
        }

        $groupId = (int)$args[0];

        if ($countArgs == 1) {
            $this->view->json([
                'items' => StatMapper::getInstance()->fetchByGroup($groupId)
            ]);
        } else {
            $period = explode('-', $args[1]);

            $from = new DateTime($period[0]);
            $to = new DateTime($period[1]);
            $to->add(new DateInterval('P1D'));

            $this->view->json([
                'items' => StatMapper::getInstance()->fetchByGroup($groupId, $from, $to)
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
