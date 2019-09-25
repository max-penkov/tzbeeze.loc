<?php

namespace App\Http\Action;


use App\components\Pagination;
use App\components\SimpleValidation;
use App\Http\models\Task;
use App\Repositories\TaskRepository;
use Engine\Template\Template;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class TaskEditAction
{

    /**
     * @var TaskRepository
     */
    private $tasks;
    /**
     * @var Template
     */
    private $template;
    /**
     * @var SimpleValidation
     */
    private $validation;

    public function __construct(TaskRepository $tasks, Template $template, SimpleValidation $validation)
    {
        $this->tasks      = $tasks;
        $this->template   = $template;
        $this->validation = $validation;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return HtmlResponse|JsonResponse
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if (!$task = $this->tasks->find($request->getAttribute('task'))) {
            return new HtmlResponse('Page not found', 404);
        }

        $form = $request->getParsedBody();
        if ($form) {
            $this->validation->validate($form);

            $task->edit($form['title'], $form['content'], $form['status'], $form['email']);
            try {
                $this->tasks->store($task);
                return new JsonResponse(['url' => '/']);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }

        return new HtmlResponse($this->template->render('app/task/edit', [
            'task' => $task,
        ]));
    }
}