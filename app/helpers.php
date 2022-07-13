<?php

if(!function_exists("sendPushNotification")){
    function sendPushNotification($userId,$template_id){
        $oneSignal = new Berkayk\OneSignal\OneSignalClient(
            env('ONESIGNAL_APP_ID'),
            env('ONESIGNAL_REST_API_KEY'),
            env('USER_AUTH_KEY')
        );
        $params = [];
        $params['include_player_ids'] = [$userId];
        $params['content_available'] = true;
        $params['template_id'] = $template_id;
        $oneSignal->setParam('priority', 10)->sendNotificationCustom($params);
        return true;
    }
}
