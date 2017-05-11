<?php

/**
 * Родительский класс для контроллеров, которым необходимо,
 *   чтобы пользователь был авторизован
 * @author ismd
 */
abstract class PsAuthController extends PsController {

    /**
     * @param PsRegistry $registry
     * @throws Exception
     */
    public function __construct(PsRegistry $registry) {
        parent::__construct($registry);

        if (is_null($this->getSession()->user)) {
            throw new Exception('Unauthorized session');
        }
    }
}
