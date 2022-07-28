<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    public const PAGINATE_LIMIT = 30;

    protected $fillable = [
        'user_id', 'action_text', 'event_id',
    ];

    public static function addToLog($text, $event_id = 0)
    {
        $data = [
            'user_id' => auth()->user()->id,
            'action_text' => $text,
            'event_id' => $event_id
        ];
        self::create($data);
    }

    public static function addToLogFromSystem($text)
    {
        $data = [
            'user_id' => 0,
            'action_text' => $text,
        ];
        self::create($data);
    }

    public function listInfo()
    {
        $result = [
            'id' => $this->id,
            'date' => Carbon::parse($this->created_at)->timestamp,
            'text' => $this->action_text,
        ];
        if ($this->user_id == 0) {
            $result['email'] = 'system';
        } else {
            if ($this->user()->first() != null) {
                $result['email'] = $this->user()->first()->email;
            } else {
                $result['email'] = '';
            }
        }
        return $result;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
