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

    public function getClient()
    {
        if(!$this->client){
            $this->client = new Client(['cookies' => true]);
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

        return $this->getClient()->post($url, $post ? ['form_params' => $post] : null);
    }

    public function getFormInputs($src)
    {
        $doc = $this->getDoc($src);
        $xpath  =   new \DOMXPath($doc);

        $form_el    =   $xpath->query('//form[@id="aspnetForm"]');
        if($form_el->length != 1){
            throw new Exception('Unable to find form');
        }

        $inputs =   $xpath->query('.//input', $form_el[0]);
        if($inputs->length == 0){
            throw new Exception('Unable to find inputs');
        }

        $post = [];
        foreach($inputs as $input){
            $post[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        $post['__EVENTTARGET']    =   'ctl00$ContentPlaceHolder1$BookingBox1$btnSearchFlights';
        $post['__EVENTARGUMENT']    =   '';

        return (object)[
            'action'    =>  $form_el[0]->getAttribute('action'),
            'post'  =>  $post
        ];
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