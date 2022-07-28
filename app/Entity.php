<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

//TODO это сущность "объекта". Из-за некачественно вобранного имени для постов(GuardedObject).
// более менее подходящее под свою роль название. Entity - объект(представил что это некая организация), которую охраняют посты.
// PS рефакторинг названия постов делать не стал
class Entity extends Model
{
    /**
     * @var int
     */
    public const INFINITY_SCROLL_PER_PAGE = 10;

    protected $fillable = [
        'id',
        'name',
        'phone',
        'comment',
        'address',
        'call_to',
        'call_from',
        'service_phone',
        'customer_name',
        'dialing_status',
        'quantity_calls',
        'central_guarded_objects_id',
        'max_duration_work',
        'call_back_quantity'
    ];

    /**
     * @return HasOne
     */
    public function centralPost(): HasOne
    {
        return $this->hasOne(GuardedObject::class, 'id', 'central_guarded_objects_id');
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(GuardedObject::class, 'entity_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(EntityCustomer::class, 'entity_id', 'id');
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'entity_manager', 'entity_id', 'user_id');
    }

    /**
     * time string to Carbon\Carbon
     *
     * @param $value
     * @return Carbon
     */
    public function getCallFromAttribute($value)
    {
        return Carbon::createFromTimeString($value);
    }

    /**
     * time string to Carbon\Carbon
     *
     * @param $value
     * @return Carbon
     */
    public function getCallToAttribute($value)
    {
        return Carbon::createFromTimeString($value);
    }

    /**
     * @return bool
     */
    public function hasCentralPost(): bool
    {
        return !is_null($this->central_guarded_objects_id);
    }

    /**
     * Генерация графика обзвона постов относительно объекта
     */
    public function generateCallQueue(): void
    {
        if ($this->quantity_calls === 0 || $this->dialing_status === 0) {
            return;
        }

        if ($this->hasCentralPost()) {
            $this->centralPost->generateDialQueue($this->call_from, $this->call_to, $this->quantity_calls);
            return;
        }

        foreach ($this->posts as $post) {
            $post->generateDialQueue($this->call_from, $this->call_to, $this->quantity_calls);
        }
    }

    /**
     * Сгенерировать график обзвона постов для всех объектов
     */
    public static function generateCallQueueForAll(): void
    {
        self::all()->each(function (Entity $entity) {
            $entity->generateCallQueue();
        });
    }
}
