<?php

use App\Http\Action\TaskCompleteAction;
use App\Http\Action\TaskCreateAction;
use App\Http\Action\TaskDeleteAction;
use App\Http\Action\TaskStoreAction;
use App\Http\Action\TaskEditAction;
use App\Http\Application;
use App\Http\Action\TaskIndexAction;
use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Middleware\ErrorMiddlewareHandler;
use App\Http\Middleware\NotFoundHandler;
use App\Http\Middleware\RouteMiddleware;
use App\Http\Pipeline\MiddlewareResolver;
use Engine\Container\Container;
use Engine\Http\Router\RouteCollection;
use Engine\Http\Router\Router;
use Engine\Template\Extension\RouteExtension;
use Engine\Template\Template;
use Engine\Template\TwigTemplate;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

require dirname(__FILE__) . '/vendor/autoload.php';

// Config
$container = new Container();
$container->set('config', [
    'debug' => true,
    'admin' => ['admin1' => '123'],
    'db'    => [
        'dsn'      => 'sqlite:db/db.sqlite',
        'username' => 'root',
        'password' => 'root',
    ],
]);
$container->set(PDO::class, function (Container $container) {
    return new PDO(
        $container->get('config')['db']['dsn'],
        $container->get('config')['db']['username'],
        $container->get('config')['db']['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
});

$container->set(Router::class, function (Container $container) {
    // routing
    $routes = new RouteCollection();
    $routes->any('taskEdit', '/edit/{task}',
        [
            new BasicAuthMiddleware($container->get('config')['admin']),
            TaskEditAction::class,
        ],
        ['task' => '\d+']);
    $routes->get('taskCreate', '/create', TaskCreateAction::class);
    $routes->post('taskStore', '/store', TaskStoreAction::class);
    $routes->get('taskPage', '/task/page/{page}', TaskIndexAction::class, ['page' => '\d+']);
//    $routes->get('taskSort', '/task/sort/{sort}', TaskIndexAction::class, ['tokens' => ['sort' => '\d+']]);
    $routes->get('taskDelete', '/task/delete/{id}', [
        new BasicAuthMiddleware($container->get('config')['admin']),
        TaskDeleteAction::class,
        ['tokens' => ['id' => '\d+']],
    ]);
    $routes->get('markDone', '/task/complete/{id}', [
        new BasicAuthMiddleware($container->get('config')['admin']),
        TaskCompleteAction::class,
        ['tokens' => ['id' => '\d+']],
    ]);
    $routes->get('taskView', '/task/{id}', TaskEditAction::class, ['tokens' => ['id' => '\d+']]);
    $routes->get('admin', '/admin', [
        new BasicAuthMiddleware($container->get('config')['admin']),
        TaskIndexAction::class,
    ]);
    $routes->get('taskIndex', '/{sort}', TaskIndexAction::class, ['sort' => '\s*|\w+', 'page' => '\s*']);
    return new Router($routes);
});
$container->set(MiddlewareResolver::class, function (Container $container) {
    return new MiddlewareResolver($container);
});
$container->set(RouteMiddleware::class, function (Container $container) {
    return new RouteMiddleware($container->get(Router::class), $container->get(MiddlewareResolver::class));
});
$container->set(Template::class, function (Container $container) {
    return new TwigTemplate($container->get(Twig\Environment::class), '.html.twig');
});
$container->set(Twig\Environment::class, function (Container $container) {
    $templateDir = 'templates';
    $loader      = new Twig\Loader\FilesystemLoader();
    $loader->addPath($templateDir);
    $environment = new Twig\Environment($loader);
    $environment->addExtension($container->get(RouteExtension::class));
    return $environment;
});
$container->set(Application::class, function (Container $container) {
    return new Application(
        $container->get(MiddlewareResolver::class),
        new NotFoundHandler()
    );
});

// Initialization
$app = $container->get(Application::class);
$app->pipe(ErrorMiddlewareHandler::class);
$app->pipe(RouteMiddleware::class);
$request = ServerRequestFactory::fromGlobals();

// run application
$response = $app->run($request);

// view
$view = new SapiEmitter();
$view->emit($response);
