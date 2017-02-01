<?php
/**
 * Project: BlueairScraper
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace projectivemotion\BlueairScraper;


use GuzzleHttp\Client;

class Scraper
{
    /**
     * @var Client
     */
    protected $client;

    protected $options  =   [];

    public function __construct($options = [])
    {
        $this->options  =   ['cookies' => true, 'base_uri'   =>  'https://open.maxitours.be/'] + $options;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        if(!$this->client){
            $this->setClient(new Client($this->options));
        }
        return $this->client;
    }

    public function api($url, $post = null)
    {
        if(is_object($url))
        {
            $post   =   $url->post;
            $url    =   $url->action;
        }

        if(!$post)
            return $this->getClient()->get($url);

        return $this->getClient()->post($url, $post ? [
            'form_params' => $post,
            'headers'   =>  [
                'User-Agent'    =>  'Mozilla/5.0 (X11; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0',
                'Accept'    =>  'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language'   =>  'en-US,en;q=0.5',
                'X-MicrosoftAjax'   =>  'Delta=true',
                'X-Requested-With'  =>  'XMLHttpRequest',
                'Referer'   =>'https://open.maxitours.be/Default.aspx'
            ]] : null);
    }

    public function getFormInputs($src)
    {
        $doc = $this->getDoc($src);
        $xpath  =   new \DOMXPath($doc);

        $form_el    =   $xpath->query('//form[@id="aspnetForm"]');
        if($form_el->length != 1){
            throw new Exception('Unable to find form');
        }

        $inputs =   $xpath->query('.//input[contains(@name, "__")]', $form_el[0]);
        if($inputs->length == 0){
            throw new Exception('Unable to find inputs');
        }

        $post = [];
        foreach($inputs as $input){
            $attribute = $input->getAttribute('name');
//            if(strpos($attribute, '__') !== 0) continue;
            $post[$attribute] = $input->getAttribute('value');
        }

        $post['__EVENTTARGET']    =   'ctl00$ContentPlaceHolder1$BookingBox1$btnSearchFlights';
        $post['__EVENTARGUMENT']    =   '';

        return (object)[
            'action'    =>  $form_el[0]->getAttribute('action'),
            'post'  =>  $post
        ];
    }

    public function createSearchPost(Query $query)
    {
        $homesrc = (string)$this->api('/Default.aspx')->getBody();
        $form   =   $this->getFormInputs($homesrc);

        $form->post   =   $query->getPost() + $form->post;
        return $form;
    }

    public function getFlights(Query $query)
    {
        $form   =   $this->createSearchPost($query);
        $redirect_response_src   =   $this->api($form)->getBody();

        $action =   $this->getRedirectForm($redirect_response_src);
        $raw_flightspage    =   $this->api($action)->getBody();

        $ctr    =   $this->parseFlightSearchResults($raw_flightspage);

        return $ctr;
    }

    public function getRedirectForm($src)
    {
        $split  =   explode('|pageRedirect|', $src);
        if(count($split) != 2)
            throw new Exception('Unexpected response.');

        $action =   urldecode(trim($split[1], '|'));
        $post   =   null;

        return (object)['action'    => $action, 'post'  => $post];
    }

    /**
     * @param $src
     * @return FlightsContainer
     * @throws Exception
     */
    public function parseFlightSearchResults($src)
    {
        $doc    =   $this->getDoc($src);
        $xpath  =   new \DOMXPath($doc);

        $cal_holder =   $xpath->query('//div[@class="calendar_holder"]');
        if($cal_holder->length != 2)
            throw new Exception('Did not find expected calendar_holder elements.');

        $flights = new FlightsContainer();
        $direction = 'outbound';
        foreach($cal_holder as $cal){

            $journeys_bydate  =   $xpath->query('div[@class="datemarket-journeys"]', $cal);

            foreach($journeys_bydate as $curdate_el){
                $dateValue  =   date_create_from_format('d.m.Y', $curdate_el->getAttribute('data-day'));
                $dateflights    =   $flights->addDate($direction, $dateValue);

                $journeys_ondate    =   $xpath->query('div[@class="datemarket-journey"]', $curdate_el);
                if($journeys_ondate->length < 1)    // no flights available?
                    continue;

                foreach($journeys_ondate as $journeyitem){
                    $segments   =   FlightsContainer::decodeFlightSegments($journeyitem->getAttribute('data-journey'));
                    $price_allpassengers    =   $xpath->query('a/div/span/s', $journeyitem)->item(0)->textContent;

                    $flights->addFlight($direction, iterator_to_array($segments), [
                        // this is the total price for all passenger tickets
                        'price_allpassengers' => FlightsContainer::parsePriceString($price_allpassengers)
                    ]);
                }
            }
            // loop..
            $direction = 'inbound';
        }
        return $flights;
    }

    /**
     * @param $body
     * @return \DOMDocument
     */
    public function getDoc($body)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $body);
        libxml_clear_errors();
        return $dom;
    }
}