<?php

namespace App\Observers;

use App\GuardedObject;

class GuardedObjectObserver
{
    /**
     * Handle the guarded object "created" event.
     *
     * @param GuardedObject $guardedObject
     * @return void
     */
    public function created(GuardedObject $guardedObject)
    {
        $entity = $guardedObject->entity;
        $guardedObject->generateDialQueue(
            $entity->call_from,
            $entity->call_to,
            $entity->quantity_calls
        );
    }

    /**
     * Handle the guarded object "updated" event.
     *
     * @param GuardedObject $guardedObject
     * @return void
     */
    public function updated(GuardedObject $guardedObject)
    {
        //
    }

    /**
     * Handle the guarded object "deleted" event.
     *
     * @param GuardedObject $guardedObject
     * @return void
     */
    public function deleted(GuardedObject $guardedObject)
    {
        //
    }

    /**
     * Handle the guarded object "restored" event.
     *
     * @param GuardedObject $guardedObject
     * @return void
     */
    public function restored(GuardedObject $guardedObject)
    {
        //
    }

    /**
     * Handle the guarded object "force deleted" event.
     *
     * @param GuardedObject $guardedObject
     * @return void
     */
    public function forceDeleted(GuardedObject $guardedObject)
    {
        //
    }
}
