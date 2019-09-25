<?php

namespace App\Http\Action;


use App\components\Pagination;
use App\Repositories\TaskRepository;
use Engine\Template\Template;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;

class TaskDeleteAction
{
    /**
     * @var \PDO
     */
    private $db;
    /*
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
     * @return HtmlResponse|RedirectResponse
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        if (!$task = $this->tasks->find($id)) {
            return new HtmlResponse('Page not found', 404);
        }
        try {
            $this->tasks->delete($id);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        return new RedirectResponse('/');
    }
}