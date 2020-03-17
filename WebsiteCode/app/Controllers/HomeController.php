<?php

namespace itechTest\App\Controllers;

/**
 * Home controller
 */
class HomeController extends BaseController
{

    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function getIndex(): void
    {
        \view()->render('home.index');
    }

    /**
     * This will return a page to indicate error 404
     *
     * @throws \Exception
     */
    public function getErrorPage(): void
    {
        \view()->setThemeLayout('blank')->render('errors.404');
    }


    /**
     * This will return the FAQ page
     *
     * @throws \Exception
     */
    public function getFaq(): void
    {

        \view()->render('faq');
    }


    /**
     * @throws \Exception
     */
    public function getThirdParty(): void
    {
        \view()->render('third-party.index');
    }


    /**
     * @throws \Exception
     */
    public function showWidget(): void
    {
        \view()->render('home.widget');
    }
}
