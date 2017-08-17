<?php

class StatController extends PsController {

    public function indexPartial() {
    }

    public function fetchAction() {
        $id = (int)$this->getArgs()[0];

        $link = LinkMapper::getInstance()->fetch([
            'id' => $id,
        ], 1);

        if (empty($link)) {
            throw new Exception('Ссылка не найдена');
        }

        $link = $link[0];

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

        $serverUrl = $this->getHelper('Server')->url();
        $link['short_link_full'] = $serverUrl . '/' . $link['short_link'];

        $this->view->json([
            'link_info' => $link,
            'stat' => $stat,
            'count' => StatMapper::getInstance()->fetchCount([
                'link_id' => $id,
            ]),
        ]);
    }
}
