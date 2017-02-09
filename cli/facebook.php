<?php

require_once '../config.php';
require_once '../vendor/autoload.php';

$m = new MongoClient();
$db = $m->selectDB('resistance_calendar');
$eventCollection = $db->selectCollection('events');

$fb = new \Facebook\Facebook([
  'app_id' => FB_APP_ID,
  'app_secret' => FB_APP_SECRET,
  'default_graph_version' => FB_GRAPH_VERSION,
  'default_access_token' => FB_ACCESS_TOKEN
]);

$eventsToCheck = $eventCollection->find([])->sort(['updated' => 1]);
foreach ($eventsToCheck as $eventToCheck) {
    try {
        $response = $fb->get("/{$eventToCheck['_id']}?fields=attending_count,category,end_time,name,place,timezone");
        $event = $response->getDecodedBody();
        $eventCollection->update(
            ['_id' => $eventToCheck['_id']],
            ['$set' => [
                'name' => (isset($event['name'])) ? $event['name'] : null,
                'attending_count' => (isset($event['attending_count'])) ? $event['attending_count'] : null,
                'start_time' =>  (isset($event['start_time'])) ? $event['start_time'] : null,
                'end_time' =>  (isset($event['end_time'])) ? $event['end_time'] : null,
                'timezone' =>  (isset($event['timezone'])) ? $event['timezone'] : null,
                'location_name' => (isset($event['place']['name'])) ? $event['place']['name'] : null,
                'location' => (isset($event['place']['location'])) ? $event['place']['location'] : null,
                'updated' => time()
            ]]
        );
    } catch (\Exception $e) {
        $eventCollection->update(
            ['_id' => $eventToCheck['_id']],
            ['$set' => [
                'error' => $e->getMessage(),
                'updated' => time()
            ]]
        );
    }
}