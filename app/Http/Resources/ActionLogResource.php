<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionLogResource extends JsonResource
{
    private static $addUserRelationship = false;

    public static function addUser(bool $add)
    {
        self::$addUserRelationship = $add;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'createdAt' => $this->created_at,
            'id' => $this->id,
            'user' => self::$addUserRelationship ? new UserResource($this->user) : $this->user_id,
            'actionText' => $this->action_text
        ];
    }
}
