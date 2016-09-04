<?php namespace Vaccuum\Foundation;

use Symfony\Component\HttpFoundation\Response;
use Vaccuum\Contracts\Container\IContainer;
use Vaccuum\Contracts\Dispatcher\IDispatcher;
use Vaccuum\Contracts\Foundation\ApplicationException;
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
    public function start()
    {
        $info = $this->router->match();

        $output = $this->dispatcher->dispatch(
            $info->arguments(),
            $info->handler()
        );

        $response = $this->ensureResponse($output);
        $response->send();

        return $response;
    }

    /**
     * Ensure output is a proper response object.
     *
     * @param mixed $output
     *
     * @throws ApplicationException
     * @return Response
     */
    protected function ensureResponse($output)
    {
        if ($output instanceof Response)
        {
            return $output;
        }
        elseif (is_string($output))
        {
            return Response::create($output, 200);
        }
        else
        {
            $message = "Action response cannot be served.";
            throw new ApplicationException($message);
        }
    }
}
