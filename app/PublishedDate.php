<?php

function getPublishedDate() {
    $client = new Client();
    $res = $client->request('GET', 'https://api.github.com/repos/gshawnr/IndustryProjLaravel/commits/master', [
        'headers' => [
            'Authorization' => 'Bearer ff51716b5d5440f9a78f1dcba88fdb8cf501f53e',
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]
    ]);

    $data = json_decode($res->getBody(), true);

    $date = new DateTime(substr(strval(collect($data['commit']['committer']['date'])), 2, 20));

    $date->modify('-7 hour');

    $formatDate = "D M, d Y";
    $formatTime = "H i A";

    $finalDate = date_format($date, $formatDate) . " at " .  date_format($date, $formatTime);

    return $finalDate;
}