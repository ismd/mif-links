<?php

class ServerActionHelper extends PsActionHelper {

    public function url() {
        $protocol = !empty($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS'] ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['SERVER_NAME'];
    }
}
