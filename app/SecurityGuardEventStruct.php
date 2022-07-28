<?php

namespace App;

use phpDocumentor\Reflection\Types\Collection;

class SecurityGuardEventStruct extends EventStruct
{
    /**
     * @var PostEventStruct[]
     */
    public $posts;

    /**
     * @var SecurityGuard
     */
    public $securityGuard;

    public function __construct(SecurityGuard $securityGuard)
    {
        $this->securityGuard = $securityGuard;
        $this->posts = collect();
    }

    public function insertPost(PostEventStruct $eventStruct)
    {
        $this->posts->push($eventStruct);
    }
}
