<?php
/**
 * В этом файле происходит распознавание ссылки и редирект
 * @author ismd
 */

class ErrorController extends PsController {

    public function indexAction() {
        $shortLink = $_SERVER['REQUEST_URI'];
        $shortLink = ltrim($shortLink, '/');

        $firstPart = strstr($shortLink, '?', true);
        if ($firstPart) {
            $shortLink = $firstPart;
        }

        $link = LinkMapper::getInstance()->fetch([
            'l.short_link' => $shortLink,
        ], 1);

        if (empty($link)) {
            header('HTTP/1.0 404 Not Found', true, 404);
            die;
        }

        $link = $link[0];

        try {
            StatMapper::getInstance()->add($link['id'], $link['group_id'], [
                'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ]);
        } catch (Exception $e) {
            PsLogger::getInstance()->log('Не удалось записать статистику ' . $shortLink);
        }

        http_response_code(301);
        header('Location: ' . $link['link']);
        die;
    }
}
