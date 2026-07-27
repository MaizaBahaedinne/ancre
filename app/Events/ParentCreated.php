<?php

namespace App\Events;

use App\Models\ParentModel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ParentModel $parent
    ) {
    }
}
