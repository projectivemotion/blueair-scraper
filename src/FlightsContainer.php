<?php
/**
 * Project: BlueairScraper
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace projectivemotion\BlueairScraper;


class FlightsContainer
{
    public $inbound =   [];
    public $outbound    =   [];

    public function &addDate($directionstr, \DateTime $date)
    {
        $datestr = $date->format('Y-m-d');
        $direction = &$this->$directionstr;
        $direction[$datestr]    =   [];

        return $direction[$datestr];
    }

    public static function parsePriceString($priceString)
    {
        if(!preg_match('#(\d+).(\d+)\W*(\D+)#', utf8_decode($priceString), $m))
            throw new Exception('unable to parse price.');

        return (object)[
            'amount'    => 1*"$m[1].$m[2]",
            'currency'  =>  strtoupper($m[3])
        ];
    }

    public function addFlight($directionstr, $segments, $prices)
    {
        $direction = &$this->$directionstr;

        $departure_segment = current($segments);
        $arrival_segment    =   end($segments);
        $departure_date =   $departure_segment->departure;
        $arrival_date   =   $arrival_segment->arrival;
        $departure_datestr  =   $departure_date->format('Y-m-d');

        if(!isset($direction[$departure_datestr]))
            $direction[$departure_datestr]    =   [];

        $direction[$departure_datestr][]    =   new Flight($prices +    [
                'departure' =>  $departure_date,
                'arrival'   =>  $arrival_date,
                'segments'  => $segments
        ]);
    }

    public static function decodeFlightSegments($journey)
    {
        $segments   =   explode('^', $journey);
        foreach($segments as $segment){
            if(!preg_match('/^(.*)~(.*)~ ~~(.*)~(.*)~(.*)~(.*)~~$/', $segment, $matches))
                throw new Exception('unable to parse segment '. $segment);
            $segmentparsed  =   new Flight([
                'operator'  => $matches[1],
                'number'  => $matches[2],
                'origin'  => $matches[3],
                'destination'  => $matches[5],
                'departure'    => date_create_from_format('m/d/Y H:i', $matches[4]),
                'arrival'  => date_create_from_format('m/d/Y H:i', $matches[6]),
            ]);
            yield $segmentparsed;
        }
    }
}