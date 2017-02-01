<?php
/**
 * Project: BlueairScraper
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

require __DIR__ . '/../vendor/autoload.php';

$Scraper = new \projectivemotion\BlueairScraper\Scraper();

$query  =   new \projectivemotion\BlueairScraper\Query();

$query->setDepartDate(date_create('2017-02-11'));
$query->setReturnDate(date_create('2017-02-14'));
$query->setOrigin('TXL');
$query->setDestination('FCO');
$query->setNumAdults(2);

$response = $Scraper->getFlights($query);


//array_walk()
//print_r($response);

echo \json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);