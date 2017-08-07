<?php
/**
 * @author ismd
 */

class IndexController extends PsController {

    private const ALLOWED_URLS = ['admin'];

    public function indexAction() {
        if (!in_array($this->registry->router->getRoute(), self::ALLOWED_URLS)) {
            throw new Exception;
        }
    }
}
