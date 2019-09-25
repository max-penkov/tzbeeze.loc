<?php

namespace App\Http\Action;


use App\components\SimpleValidation;
use App\Http\models\Task;
use App\Repositories\TaskRepository;
use Engine\Template\Template;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;

class TaskStoreAction
{

    /**
     * @var Template
     */
    private $template;
    /**
     * @var TaskRepository
     */
    private $tasks;
    /**
     * @var SimpleValidation
     */
    private $validation;

    public function __construct(TaskRepository $tasks, Template $template, SimpleValidation $validation)
    {
        $this->template   = $template;
        $this->tasks      = $tasks;
        $this->validation = $validation;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return JsonResponse|RedirectResponse
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $form = $request->getParsedBody();
        $this->validation->validate($form);

        $task = new Task();

        $task->create($form['title'], $form['content'], $form['email'], $form['user_name']);
        try {
            $this->tasks->store($task);
            return new JsonResponse(['url' => '/']);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        return new RedirectResponse('/', 302);
    }
}