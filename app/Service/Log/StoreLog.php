<?php

namespace App\Service\Log;

use App\Log;
use Illuminate\Support\Facades\Auth;

class StoreLog
{
    protected $message;
    protected $accessed_route;
    protected $user_agent;

    public function __construct($message, $accessed_route, $user_agent)
    {
        $this->message = $message;
        $this->accessed_route = $accessed_route;
        $this->user_agent = $user_agent;
    }

    public function storeLogInformation()
    {
        $log = new Log();
        $log->user_id = Auth::user()->id;
        $log->user_agent = $this->user_agent;
        $log->message = $this->message;
        $log->accessed_route = $this->accessed_route;
        $log->save();
        return $log;
    }
}
