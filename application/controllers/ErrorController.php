<?php
/**
 * В этом файле происходит распознавание ссылки и редирект
 * @author ismd
 */

class ErrorController extends PsController {

    public function indexAction() {
        $route = trim($_GET['route'], '/');

        $link = LinkMapper::getInstance()->fetch([
            'short_link' => $route,
        ], 1);

        if (!empty($link)) {
            http_response_code(301);
            header('Location: ' . $link[0]['link']);
            die;
        }
    }
}
