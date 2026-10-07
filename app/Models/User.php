<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ADMINISTRATOR = 'Administrator';

    public const STAFF = 'Staff';

    public const THESIS_COORDINATOR = 'Thesis Coordinator';

    public const FACULTY_ADVISER = 'Faculty Adviser';

    public const PANEL_MEMBER = 'Panel Member';

    public const STUDENT = 'Student';

    /**
     * Get the roles that may be assigned to users.
     *
     * @return list<string>
     */
    public static function roles(): array
    {
        return [
            self::ADMINISTRATOR,
            self::STAFF,
            self::THESIS_COORDINATOR,
            self::FACULTY_ADVISER,
            self::PANEL_MEMBER,
            self::STUDENT,
        ];
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'workplace',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function issueReports(): HasMany
    {
        return $this->hasMany(IssueReport::class);
    }
}
