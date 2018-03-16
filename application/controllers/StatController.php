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

    public function fetchPeriodByGroupAction() {
        $args = $this->getArgs();

        if (count($args) < 3) {
            throw new Exception('Неправильный запрос');
        }

        $this->view->json([
            'items' => StatMapper::getInstance()->fetchPeriodByGroup(new DateTime($args[0]),
                                                                     new DateTime($args[1]),
                                                                     (int)$args[2])
        ]);
    }
}
