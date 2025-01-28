<?php

namespace App\Service;

use App\Entity\Task;

class TaskService
{
    private const EDITABLE_PERIOD_DAYS = 7;

    public function canEdit(Task $task): bool
    {
        $createdAt = $task->getCreatedAt();
        $now = new \DateTime();
        $interval = $now->diff($createdAt);

        return $interval->days < self::EDITABLE_PERIOD_DAYS;
    }
}
