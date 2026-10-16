<?php

namespace App\Support;

class CurrentContext
{
    public function accountId(): int
    {
        return (int) config('app.account_id'); 
    }

    public function projectId(): int
    {
        return (int) config('app.project_id'); 
    }
}