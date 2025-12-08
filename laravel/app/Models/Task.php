<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use ReflectionClass;

/**
 *
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static TaskFactory factory($count = null, $state = [])
 * @method static Builder|Task newModelQuery()
 * @method static Builder|Task newQuery()
 * @method static Builder|Task query()
 * @method static Builder|Task whereCreatedAt($value)
 * @method static Builder|Task whereDescription($value)
 * @method static Builder|Task whereId($value)
 * @method static Builder|Task whereStatus($value)
 * @method static Builder|Task whereTitle($value)
 * @method static Builder|Task whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status'];

    public const NEW = 'new';
    public const IN_PROGRESS = 'in_progress';
    public const DONE = 'done';

    public function setStatus($status): void
    {
        if ($this->status != $status && self::validate($status)) {
            $this->status = $status;
        }
    }

    public static function validate($status): bool
    {
        $constants = [self::NEW, self::IN_PROGRESS, self::DONE];
        return in_array($status, $constants);
    }

}
