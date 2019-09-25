<?php

namespace App\Repositories;


use App\Http\models\Task;

class TaskRepository
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return int
     */
    public function countAll(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM tasks');
        return $stmt->fetchColumn();
    }

    /**
     * @param int    $offset
     * @param int    $limit
     *
     * @param string $sort
     *
     * @return array
     */
    public function getAll(int $offset, int $limit, string $sort = null): array
    {
        $sort   = !isset($sort) || $sort == '' ? 'date': $sort;
        $query = 'SELECT * FROM tasks ORDER BY ' . $sort;
        $stmt  = $this->pdo->prepare($query . ' ASC LIMIT ? OFFSET ?');
        $stmt->execute([$limit, $offset]);

        return array_map([$this, 'hydratePost'], $stmt->fetchAll());
    }

    /**
     * @param $id
     *
     * @return Task|null
     * @throws \Exception
     */
    public function find($id): ?Task
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        return ($row = $stmt->fetch()) ? $this->hydratePost($row) : null;
    }

    /**
     * @param Task $task
     *
     * @return Task|null
     * @throws \Exception
     */
    public function store(Task $task): ?Task
    {
        if (!$task->id) {
            $sql = "INSERT INTO tasks (title, user_name, email, status, content, date) VALUES (:title, :userName, :email, :status, :content, :date)";
        } else {
            $sql = "UPDATE tasks SET status=:status, user_name=:userName, email=:email, title=:title, content=:content, date=:date WHERE id=:id";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_filter([
            'id'       => $task->id ?? false,
            'title'    => $task->title,
            'userName' => $task->userName,
            'email'    => $task->email,
            'status'   => $task->status,
            'content'  => $task->content,
            'date'     => $task->date,
        ]));

        return $this->find($this->pdo->lastInsertId());
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        if (!$stmt->execute([$id])) {
            return false;
        };

        return true;
    }

    /**
     * @param array $row
     *
     * @return Task
     * @throws \Exception
     */
    private function hydratePost(array $row): Task
    {
        $task = new Task();

        $task->id       = (int)$row['id'];
        $task->title    = $row['title'];
        $task->content  = $row['content'];
        $task->status   = $row['status'];
        $task->date     = $row['date'];
        $task->email    = $row['email'];
        $task->userName = $row['user_name'];

        return $task;
    }
}