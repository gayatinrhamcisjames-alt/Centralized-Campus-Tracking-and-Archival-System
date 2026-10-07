<?php

namespace App\Policies;

use App\Models\Thesis;
use App\Models\User;

class ThesisPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, User::roles(), true);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Thesis $thesis): bool
    {
        if ($user->role === User::STUDENT) {
            return $thesis->student_id === $user->id;
        }

        if (in_array($user->role, [User::ADMINISTRATOR, User::STAFF, User::THESIS_COORDINATOR], true)) {
            return true;
        }

        return $thesis->adviser_id === $user->id
            || $thesis->panelMembers()->whereKey($user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === User::STUDENT;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Thesis $thesis): bool
    {
        if ($user->role === User::STUDENT) {
            return $thesis->student_id === $user->id
                && in_array($thesis->status, [Thesis::DRAFT, Thesis::REJECTED, Thesis::REVISION_REQUIRED], true);
        }

        return in_array($user->role, [User::ADMINISTRATOR, User::STAFF, User::THESIS_COORDINATOR], true)
            || ($user->role === User::FACULTY_ADVISER && $thesis->adviser_id === $user->id)
            || ($user->role === User::PANEL_MEMBER && $thesis->panelMembers()->whereKey($user->id)->exists());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Thesis $thesis): bool
    {
        return $thesis->status !== Thesis::ARCHIVED
            && ($user->role !== User::STUDENT || $thesis->student_id === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Thesis $thesis): bool
    {
        return in_array($user->role, [User::ADMINISTRATOR, User::STAFF, User::THESIS_COORDINATOR], true);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Thesis $thesis): bool
    {
        return $user->role === User::ADMINISTRATOR;
    }

    public function archive(User $user, Thesis $thesis): bool
    {
        return in_array($user->role, [User::ADMINISTRATOR, User::STAFF, User::THESIS_COORDINATOR], true);
    }

    public function assignReviewer(User $user, Thesis $thesis): bool
    {
        return in_array($user->role, [User::ADMINISTRATOR, User::THESIS_COORDINATOR], true);
    }

    public function review(User $user, Thesis $thesis): bool
    {
        return $user->role === User::STAFF
            || ($user->role === User::FACULTY_ADVISER && $thesis->adviser_id === $user->id)
            || ($user->role === User::PANEL_MEMBER && $thesis->panelMembers()->whereKey($user->id)->exists());
    }
}
