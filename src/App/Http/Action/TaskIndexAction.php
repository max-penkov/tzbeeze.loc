<?php

namespace App\Http\Action;


use App\components\Pagination;
use App\Http\Middleware\BasicAuthMiddleware;
use App\Repositories\TaskRepository;
use Engine\Template\Template;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class TaskIndexAction
{
    /**
     * @var \PDO
     */
    private $db;
    /**
     * @var int
     */
    private const PER_PAGE = 3;
    /**
     * @var Template
     */
    private $template;
    /**
     * @var TaskRepository
     */
    private $tasks;

    public function __construct(\PDO $db, TaskRepository $tasks, Template $template)
    {
        $this->db       = $db;
        $this->template = $template;
        $this->tasks    = $tasks;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return HtmlResponse
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $pager = new Pagination(
            $this->tasks->countAll(),
            $request->getAttribute('page') ?: 1,
            self::PER_PAGE
        );
        $tasks = $this->tasks->getAll(
            $pager->getOffset(),
            $pager->getLimit(),
            $request->getAttribute('sort')
        );
        return new HtmlResponse($this->template->render('app/task/index', [
            'tasks'   => $tasks,
            'pager'   => $pager,
            'isAdmin' => isset($request->getServerParams()['PHP_AUTH_USER']) ? true: false,
        ]));
    }
}