<?php

namespace App\components;


class Pagination
{
    private $totalCount;
    private $page;
    private $perPage;

    /**
     * Pagination constructor.
     *
     * @param int $totalCount
     * @param int $page
     * @param int $perPage
     */
    public function __construct(int $totalCount, int $page, int $perPage)
    {
        $this->totalCount = $totalCount;
        $this->page       = $page;
        $this->perPage    = $perPage;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPagesCount(): int
    {
        return ceil($this->totalCount / $this->perPage);
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}