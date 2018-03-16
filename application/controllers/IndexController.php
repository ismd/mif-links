<?php
/**
 * @author ismd
 */

class IndexController extends PsController {

    private const ALLOWED_URLS = ['/^admin/'];

    public function indexAction() {
        $route = $this->registry->router->getRoute();

        foreach (self::ALLOWED_URLS as $url) {
            if (preg_match($url, $route)) {
                return;
            }
        }

        throw new Exception('Not allowed url');
    }

    public function listTablePartial() {
    }

    public function visitsChartPartial() {
    }
}
