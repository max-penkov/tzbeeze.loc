<?php

namespace App\Http\Action;


use App\components\Pagination;
use App\Repositories\TaskRepository;
use Engine\Template\Template;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class TaskCreateAction
{
    /**
     * @var Template
     */
    private $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return HtmlResponse
     */
    public function __invoke(ServerRequestInterface $request)
    {
        return new HtmlResponse($this->template->render('app/task/create'));
    }
}