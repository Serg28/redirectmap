<?php
namespace Litvin\Redirectmap;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Litvin\Redirectmap\Models\RedirectMap;

class Decorator implements ExceptionHandler
{
    protected $handler;
    /**
     * Set the dependencies.
     *
     * @param    Illuminate\Contracts\Debug\ExceptionHandler    $handler
     * @return    void
     */
    public function __construct(ExceptionHandler $handler)
    {
        $this->handler  = $handler;
    }
    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function report(\Throwable $e)
    {
        $this->handler->report($e);
    }

    public function reportable(callable $reportUsing)
    {
        if (method_exists($this->handler, 'reportable')) {
            return $this->handler->reportable($reportUsing);
        }
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            $path = \Request::path();
            $map = RedirectMap::where('old_link',$path)
                ->orWhere('old_link',$path.'/')
                ->orWhere('old_link','/'.$path.'/')
                ->orWhere('old_link','/'.$path)
                ->first();

            if ($map) return \Response::redirectTo($map->new_link);
        }

        return $this->handler->render($request, $e);
    }
    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Exception  $e
     * @return void
     */
    public function renderForConsole($output, Exception $e)
    {
        $this->handler->renderForConsole($output, $e);
    }
}
