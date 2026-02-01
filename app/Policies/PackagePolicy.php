<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Package;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackagePolicy
{
    use HandlesAuthorization;

    /**
     * هل يمكن للمستخدم رؤية قائمة الحزم؟
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Package');
    }

    /**
     * هل يمكن للمستخدم رؤية بيانات حزمة محددة؟
     */
    public function view(AuthUser $authUser, Package $package): bool
    {
        return $authUser->can('View:Package');
    }

    /**
     * هل يمكن للمستخدم إضافة حزمة جديدة؟
     */
    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Package');
    }

    /**
     * هل يمكن للمستخدم تعديل بيانات حزمة؟
     */
    public function update(AuthUser $authUser, Package $package): bool
    {
        return $authUser->can('Update:Package');
    }

    /**
     * هل يمكن للمستخدم حذف حزمة؟
     */
    public function delete(AuthUser $authUser, Package $package): bool
    {
        return $authUser->can('Delete:Package');
    }

    /**
     * هل يمكن للمستخدم استعادة حزمة محذوفة؟
     */
    public function restore(AuthUser $authUser, Package $package): bool
    {
        return $authUser->can('Restore:Package');
    }

    /**
     * هل يمكن للمستخدم الحذف النهائي لحزمة؟
     */
    public function forceDelete(AuthUser $authUser, Package $package): bool
    {
        return $authUser->can('ForceDelete:Package');
    }

    /**
     * هل يمكن للمستخدم الحذف النهائي لمجموعة حزم؟
     */
    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Package');
    }

    /**
     * هل يمكن للمستخدم استعادة مجموعة حزم؟
     */
    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Package');
    }

    /**
     * هل يمكن للمستخدم نسخ حزمة؟
     */
    public function replicate(AuthUser $authUser, Package $package): bool
    {
        return $authUser->can('Replicate:Package');
    }

    /**
     * هل يمكن للمستخدم إعادة ترتيب الحزم؟
     */
    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Package');
    }
}
