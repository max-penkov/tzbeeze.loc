<?php


namespace Engine\Template;


interface Template
{
    public function render($name, array $params = []): string;
}