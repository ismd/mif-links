<?php
/**
 * В этом файле происходит распознавание ссылки и редирект
 * @author ismd
 */

class ErrorController extends PsController {

    public function indexAction() {
        $link = LinkMapper::getInstance()->fetch([
            'l.short_link' => ltrim($_SERVER['REQUEST_URI'], '/'),
        ], 1);

        if (empty($link)) {
            header('HTTP/1.0 404 Not Found', true, 404);
            die;
        }

        $link = $link[0];

        StatMapper::getInstance()->add($link['id'], [
            'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null,
            'ip' => $_SERVER['REMOTE_ADDR'],
        ]);

        http_response_code(301);
        header('Location: ' . $link['link']);
        die;
    }
}
