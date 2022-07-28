<?php

namespace App;

use App\Http\Resources\PostResource;
use Illuminate\Support\Collection;

class PostEventStruct extends EventStruct
{
    /**
     * @var GuardedObject|PostResource
     */
    public $post;

    /**
     * @var WorkShift
     */
    public $workShift;

    /**
     * @param GuardedObject|PostResource $post
     */
    public function __construct($post, Collection $workShift)
    {
        $this->post = $post;
        $this->workShift = $workShift;
    }
}
