<?php namespace vanhenry\minify;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Response;

class PackageMiddleware extends \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode {

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance())
        {
            throw new HttpException(503);
        }
        $fw = $this->app->make("Firewall");
        $fw->open();
        $response = $next($request);
        return $this->minifyHtml($response);
    }
    private function minifyHtml($response){
        if ($this->isResponseObject($response) && $this->isHtmlResponse($response) ) {
            $search = array(
                '/\>[^\S ]+/s', 
                '/[^\S ]+\</s', 
                 '/(\s)+/s', 
              '#(?://)?<!\[CDATA\[(.*?)(?://)?\]\]>#s' 
              );
             $replace = array(
                 '>',
                 '<',
                 '\\1',
              "//&lt;![CDATA[\n".'\1'."\n//]]>"
              );
            $response->setContent(preg_replace($search, $replace, $response->getContent()));
        }
        
        return $response;
    }
    private function isResponseObject($response)
    {
        return is_object($response) && $response instanceof \Illuminate\Http\Response;
    }

    private function isHtmlResponse($response)
    {
        return strtolower(strtok($response->headers->get('Content-Type'), ';')) === 'text/html';
    }
}