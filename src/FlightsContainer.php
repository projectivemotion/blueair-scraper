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
//        array_push($this->$direction, $foo);
    }

    public function addFlight($directionstr, $segments, $data)
    {
        $direction = &$this->$directionstr;

        $departure_segment = current($segments);
        $arrival_segment    =   end($segments);
        $departure_date =   $departure_segment->departure_date;
        $arrival_date   =   $arrival_segment->arrival_date;
        $departure_datestr  =   $departure_date->format('Y-m-d');

        if(!isset($direction[$departure_datestr]))
            $direction[$departure_datestr]    =   [];


        $direction[$departure_datestr][]    =   (object)($data +    [
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
            $segmentparsed  =   (object)[
                'operator'  => $matches[1],
                'number'  => $matches[2],
                'origin'  => $matches[3],
                'destination'  => $matches[5],
                'departure_date'    => date_create_from_format('m/d/Y H:i', $matches[4]),
                'arrival_date'  => date_create_from_format('m/d/Y H:i', $matches[6]),
            ];
            yield $segmentparsed;
        }
    }
}