<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DemoController
 * @Route(service="app.demo_controller")
 */
class DemoController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * DemoController constructor.
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/demo", name="demo_controller_index")
     *
     * @return string
     */
    public function index()
    {
        $html = $this->twig->render('::demo.html.twig');

        $response = new Response($html);

        return $response;
    }
}
