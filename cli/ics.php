<?php

require_once '../config.php';
require_once '../vendor/autoload.php';

use ICal\ICal;

$m = new MongoClient();
$db = $m->selectDB('resistance_calendar');
$eventCollection = $db->selectCollection('events');

$ical = new ICal('http://tockify.com/api/feeds/ics/resistance.calendar');
echo $ical->eventCount.PHP_EOL;
$events = $ical->eventsFromRange(date('Y-m-d H:i:s'));

foreach($events as $event) {
    $uid = $event->uid;


    $matches = [];
    preg_match('/https:\/\/www.facebook.com\/events\/([0-9]*)/', $event->description, $matches);
    foreach($matches as $match) {
        //Insert into mongo and set the write concern to 0 to ignore duplicate errors
        //$eventCollection->insert(['_id' => $match[1]], ['w' => 0]);
    }
}