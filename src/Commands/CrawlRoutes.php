<?php

namespace NawrasBukhari\Restricted\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class CrawlRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restricted:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawls and indexes the application routes as restricted words';

    /**
     * The router instance.
     *
     * @var Router
     */
    protected Router $router;

    /**
     * An array of all the registered routes.
     *
     * @var RouteCollection
     */
    protected RouteCollection $routes;

    /**
     * A string which represents the path to the file
     * holding the list of restricted words
     */
    protected $fileName;

    /**
     * Create a new route command instance.
     *
     * @param  Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct();
        $this->router = $router;
        $this->routes = $router->getRoutes();
        $this->fileName = config('restricted.file_path') ?: public_path('reserved.txt');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info($this->crawl().' words indexed from this crawl');
    }

    public function crawl()
    {
        $routes = $this->routes;
        $routeCollection = $routes;
        $data = [];

        foreach ($routeCollection as $route) {
            $limit = config('restricted.index_level') ?: 1;
            $paths = explode('/', $route->uri());

            foreach ($paths as $i => $path) {
                if ($i >= $limit) {
                    break;
                }
                if (! preg_match("/^\w+$/", $paths[$i])) {
                    continue;
                }
                $data[] = $path;
            }
        }

        $data = collect($data)->unique();
        $this->store($data);

        return $data->count();
    }

    public function store($routes)
    {
        $fileName = $this->fileName;

        if (config('restricted.merge') && file_exists($fileName)) {
            $old = collect(explode(PHP_EOL, file_get_contents($fileName)))
                ->map(function ($value) {
                    return trim($value);
                })->all();
            $routes = $routes->merge($old);
        }

        $input = $routes->unique()->sort()->implode(PHP_EOL);

        $file = fopen($fileName, 'w+');
        fwrite($file, $input);
        fclose($file);
    }
}
