<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thesis extends Model
{
    use HasFactory, SoftDeletes;

    public const DRAFT = 'Draft';

    public const SUBMITTED = 'Submitted';

    public const UNDER_REVIEW = 'Under Review';

    public const APPROVED = 'Approved';

    public const REJECTED = 'Rejected';

    public const ARCHIVED = 'Archived';

    public const PROPOSAL_SUBMITTED = 'Proposal Submitted';

    public const PROPOSAL_APPROVED = 'Proposal Approved';

    public const IN_DEVELOPMENT = 'In Development';

    public const FOR_DEFENSE = 'For Defense';

    public const REVISION_REQUIRED = 'Revision Required';

    protected $fillable = [
        'title', 'abstract', 'keywords', 'program', 'department', 'academic_year', 'school_year',
        'student_id', 'adviser_id', 'status', 'file_path',
        'submitted_at', 'approved_at', 'archived_at', 'defense_date',
        'is_archived',
    ];

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [self::DRAFT, self::PROPOSAL_SUBMITTED, self::PROPOSAL_APPROVED, self::IN_DEVELOPMENT, self::FOR_DEFENSE, self::REVISION_REQUIRED, self::APPROVED, self::ARCHIVED, self::REJECTED];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_students', 'thesis_id', 'student_id')->withPivot('is_lead')->withTimestamps();
    }

    public function panelMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_panels', 'thesis_id', 'panel_member_id')->withPivot('role')->withTimestamps();
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ThesisStatusHistory::class)->latest();
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ThesisMilestone::class)->orderBy('due_date');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ThesisDocument::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ThesisReview::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'archived_at' => 'datetime',
            'defense_date' => 'date',
            'is_archived' => 'boolean',
        ];
    }
}
