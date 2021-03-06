<?php

namespace SellerCenter\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Renderer
{
    /**
     * @var Environment
     */
    protected $twig;

    public function __construct()
    {
        $this->twig = new Environment(new FilesystemLoader(
            dirname(__DIR__,2).'/views'
        ));
    }

    /**
     * @param array $data
     *
     * @return string
     */
    abstract public function render(array $data): string;
}