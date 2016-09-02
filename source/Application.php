<?php namespace Vaccuum\Foundation;

use Vaccuum\Contracts\Container\IContainer;
use Vaccuum\Contracts\Dispatcher\IDispatcher;
use Vaccuum\Contracts\Foundation\IApplication;
use Vaccuum\Contracts\Router\IRouter;

class Application implements IApplication
{
    /** @var IContainer */
    protected $container;

    /** @var IRouter */
    protected $router;

    /** @var IDispatcher */
    protected $dispatcher;

    /**
     * Application constructor.
     *
     * @param IContainer  $container
     * @param IRouter     $router
     * @param IDispatcher $dispatcher
     */
    public function __construct(
        IContainer $container,
        IRouter $router,
        IDispatcher $dispatcher
    )
    {
        $this->container = $container;
        $this->router = $router;
        $this->dispatcher = $dispatcher;

        $this->container->share($this);
    }

    /** @inheritdoc */
    public function execute()
    {
        $info = $this->router->match();
        $this->dispatcher->dispatch($info->arguments(), $info->handler());
    }
}