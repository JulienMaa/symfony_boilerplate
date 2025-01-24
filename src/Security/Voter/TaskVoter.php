<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TaskVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If the user is anonymous, deny access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        // Admin users can perform any action
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user, $task);

            case self::VIEW:
                return $this->canView($user, $task);

            case self::DELETE:
                return $this->canDelete($user, $task);
        }

        return false;
    }

    // Check if the user can edit the task
    private function canEdit(UserInterface $user, Task $task): bool
    {
        // Only the author of the task can edit it
        return $task->getAuthor() === $user;
    }

    // Check if the user can view the task
    private function canView(UserInterface $user, Task $task): bool
    {
        // A user can only view tasks that they authored
        return $task->getAuthor() === $user;
    }

    // Check if the user can delete the task
    private function canDelete(UserInterface $user, Task $task): bool
    {
        // Users cannot delete tasks, even if they are the author
        return false;
    }
}
