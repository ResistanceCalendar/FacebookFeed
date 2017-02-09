<?php

require_once '../config.php';
require_once '../vendor/autoload.php';

use ICal\ICal;

$m = new MongoDB\Client();
$eventCollection = $m->resistance_calendar->events;

$ical = new ICal('http://tockify.com/api/feeds/ics/resistance.calendar');
echo $ical->eventCount.PHP_EOL;
$events = $ical->eventsFromRange(date('Y-m-d H:i:s'));

foreach($events as $event) {
    $uid = $event->uid;

    $matches = [];
    preg_match_all('/https:\/\/www.facebook.com\/events\/([0-9]*)/', $event->description, $matches);
    foreach($matches[1] as $match) {        
        if ($match) {            
            $eventCollection->updateOne(['_id' => $match], ['$set' => ['_id' => $match]], ['upsert' => true]);
        }
    }
}