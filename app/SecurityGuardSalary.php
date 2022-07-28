<?php

namespace App;

use Carbon\Carbon;

class SecurityGuardSalary
{
    /**
     * @var int
     */
    public $salary = 0;

    /**
     * @var Carbon
     */
    public $from;

    /**
     * @var Carbon
     */
    public $to;

    /**
     * @var string
     */
    public $shiftStatus;

    /**
     * @var SecurityGuard
     */
    public $securityGuard;

    /**
     * @var GuardedObject
     */
    public $post;

    public function __construct(GuardedObject $post, SecurityGuard $securityGuard, string $shiftStatus, Carbon $from = null, Carbon $to = null)
    {
        $this->post = $post;
        $this->securityGuard = $securityGuard;
        $this->from = $from;
        $this->to = $to;
        $this->shiftStatus = $shiftStatus;
    }
}
