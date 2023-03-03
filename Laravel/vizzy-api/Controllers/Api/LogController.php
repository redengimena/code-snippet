<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ActivityLogTrait;

class LogController extends Controller
{
    use ActivityLogTrait;

    function activity(Request $request) {

        $action = $request->input('action_type');
        $keys = array('target_type','show_id','episode_id','card_id','tray_icon','content');
        $fields = [];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                $fields[$key] = $request->input($key);
            }
        }

        try {
            $this->log($action, $fields);
        }
        catch (\Exception $e) {
            return $this->sendError('Error saving activity. ' . $e->getMessage(), 200);
        }

        return $this->sendResponse('','Saved successfully');
    }
}
