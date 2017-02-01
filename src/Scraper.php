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
            $this->client = new Client(['cookies' => true, 'base_uri'   =>  'https://open.maxitours.be/']);
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

        return $this->getClient()->post('/Default.aspx', $post ? [
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

        $inputs =   $xpath->query('.//input', $form_el[0]);
        if($inputs->length == 0){
            throw new Exception('Unable to find inputs');
        }

        $post = [];
        foreach($inputs as $input){
            $attribute = $input->getAttribute('name');
            if(strpos($attribute, '__') !== 0) continue;
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
//        $homesrc = (string)$this->api('/Default.aspx')->getBody();
//        $form   =   $this->getFormInputs($homesrc);
        $form = (object)[
            'action'    =>  '/Default.aspx',
            'post'  => [
                '__EVENTTARGET' =>  'ctl00$ContentPlaceHolder1$BookingBox1$btnSearchFlights',
                '__EVENTARGUMENT'   =>  '',
                '__VIEWSTATEGENERATOR'  =>  'CA0B0334',
                '__VIEWSTATE' => '/wEPDwULLTE1Njc5Nzk2NDcPFgYeDm1haW5fcm91bmR0cmlwZx4ORmxleGlibGVEYXlzTm8CAh4TUm91dGVzUm91bmR0cmlwT25seQU/TENBLUJHV3xCR1ctTENBfExDQS1FQkx8RUJMLUxDQXxUUk4tTUFIfE1BSC1UUk58VFJOLVBNSXxQTUktVFJOFgJmD2QWAgIBD2QWGgIBDw8WAh4LTmF2aWdhdGVVcmwFDn4vRGVmYXVsdC5hc3B4ZGQCBA9kFgJmD2QWAgIBDxYCHgtfIUl0ZW1Db3VudGZkAgcPZBZGAgEPDxYEHwMFJWh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vRmlyc3QtUGFnZS8eBlRhcmdldAUEX3RvcGRkAgUPDxYEHwMFN2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vRGVzdGluYXRpb25zL0Rlc3RpbmF0aW9ucy1NYXAfBQUEX3RvcGRkAgcPDxYEHwMFN2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vRGVzdGluYXRpb25zL0ZsaWdodHMtU2NoZWR1bGUfBQUEX3RvcGRkAgkPDxYEHwMFQGh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vRGVzdGluYXRpb25zL0Rlc3RpbmF0aW9ucy1BbmQtQWlycG9ydHMfBQUEX3RvcGRkAgsPDxYCHwMFI2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vU2VydmljZXMvZGQCDQ8PFgQfAwUyaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9TZXJ2aWNlcy9Mb3VuZ2UtUHJvdG9jb2wfBQUEX3RvcGRkAg8PDxYEHwMFNWh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vU2VydmljZXMvR3JvdXAtUmVzZXJ2YXRpb25zHwUFBF90b3BkZAIRDw8WBB8DBSpodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2VzL0NoYXJ0ZXIfBQUEX3RvcGRkAhMPDxYCHwMFLmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vU2VydmljZXMvQmx1ZS1CaXN0cm9kZAIVDw8WBB8DBShodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2VzL0NhcmdvHwUFBF90b3BkZAIXDw8WBB8DBStodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2VzL0UtQm9yZGVyHwUFBF90b3BkZAIbDw8WBB8DBS5odHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2VzL0FkdmVydGlzaW5nHwUFBF90b3BkZAIdDw8WAh8DBS1odHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2VzL0UtU2VydmljZXNkZAIfDw8WAh8DBTdodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2VzL1NodXR0bGUtQnVzLVRyYW5zZmVyZGQCIQ8PFgQfAwUvaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9Db3Jwb3JhdGUtUHJvZ3JhbS9OZXcfBQUEX3RvcGRkAiMPDxYEHwMFKGh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vQWdlbmNpZXMvSW5kZXgfBQUEX3RvcGRkAicPDxYEHwMFMGh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVXRpbC1JbmZvcy9MYXRlc3QtTmV3cx8FBQRfdG9wZGQCKQ8PFgQfAwUxaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9VdGlsLUluZm9zL0dpZnQtVm91Y2hlch8FBQRfdG9wZGQCKw8PFgQfAwUpaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9PZmZlcnMtSGlzdG9yeS8fBQUEX3RvcGRkAi0PDxYEHwMFMWh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVXRpbC1JbmZvcy9UcmF2ZWwtR3VpZGUfBQUEX3RvcGRkAi8PDxYEHwMFLmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vQ2FycmlhZ2UtQ29uZGl0aW9ucy8fBQUEX3RvcGRkAjEPDxYEHwMFMGh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVXRpbC1JbmZvcy9DYWxsLUNlbnRlch8FBQRfdG9wZGQCMw8PFgQfAwUvaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9VdGlsLUluZm9zL05ld3NsZXR0ZXIfBQUEX3RvcGRkAjUPDxYEHwMFN2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVXRpbC1JbmZvcy9TcGVjaWFsLUFzc2lzdGFuY2UfBQUEX3RvcGRkAjkPDxYEHwMFKmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vQ29tcGFueS9BYm91dC1Vcx8FBQRfdG9wZGQCOw8PFgQfAwUnaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9Db21wYW55L0NyZXdzHwUFBF90b3BkZAI9Dw8WBB8DBSdodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0NvbXBhbnkvRmxlZXQfBQUEX3RvcGRkAj8PDxYEHwMFKGh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vQ29tcGFueS9TYWZldHkfBQUEX3RvcGRkAkEPDxYEHwMFImh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vQ2FyZWVycy8fBQUEX3RvcGRkAkMPDxYEHwMFKmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVXRpbC1JbmZvcy9QcmVzcx8FBQRfdG9wZGQCRQ8PFgQfAwUiaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9Db250YWN0Lx8FBQRfdG9wZGQCRw8PFgIfAwUpaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS8vQ29tcGFuaWUvU3RhZmZkZAJJDw8WBB8DBTNodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0NvbXBhbnkvSW5mbGlnaHQtTWFnYXppbmUfBQUEX3RvcGRkAksPDxYEHwUFBl9ibGFuax8DBSBodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL3Jzcy1lbmRkAk0PDxYCHwMFKGh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVXRpbC1JbmZvcy9Cb3hkZAIID2QWBAIBD2QWAmYPZBYEAgEPDxYGHwECAh4KX3JvdW5kdHJpcGcfAgU/TENBLUJHV3xCR1ctTENBfExDQS1FQkx8RUJMLUxDQXxUUk4tTUFIfE1BSC1UUk58VFJOLVBNSXxQTUktVFJOZBYUAgMPEA8WBh4ORGF0YVZhbHVlRmllbGQFBElBVEEeDURhdGFUZXh0RmllbGQFBE5hbWUeC18hRGF0YUJvdW5kZ2QQFTYJU2VsZWN0Li4uKUJydXNzZWxzIC0gQnJ1eGVsbGVzIEFpcnBvcnQgLSBUZXJtaW5hbCBCGUxhcm5hY2EgLSBMYXJuYWNhIEFpcnBvcnQfQ29wZW5oYWdlbiAtIENvcGVuaGFnZW4gQWlycG9ydBtCb3JkZWF1eCAtIEJvcmRlYXV4IEFpcnBvcnQfTHlvbiAtIFN0LiBFeHVwZXJ5IChUZXJtaW5hbCAxKSBOaWNlIC0gQ8O0dGUgZCdBenVyIC0gVGVybWluYWwgMRtQYXJpcyAoQmVhdXZhaXMpIC0gQmVhdXZhaXMOQmVybGluIC0gVGVnZWwmSGFtYnVyZyAtIEhhbWJ1cmcgQWlycG9ydCAoVGVybWluYWwgMikdS29sbiAtIEtvbG4gQm9ubiAtIFRlcm1pbmFsIDIqU3R1dHRnYXJ0IC0gU3R1dHRnYXJ0IEFpcnBvcnQgKFRlcm1pbmFsIDMpH0Jpcm1pbmdoYW0gLSBCaXJtaW5naGFtIEFpcnBvcnQ0R2xhc2dvdyAtIEdsYXNnb3cgSW50ZXJuYXRpb25hbCBBaXJwb3J0IChUZXJtaW5hbCAxKR9MaXZlcnBvb2wgLSBKb2huIExlbm5vbiBBaXJwb3J0FkxvbmRvbiAoTHV0b24pIC0gTHV0b24lQXRoZW5zIC0gQXRoZW5zIEludGVybmF0aW9uYWwgQWlycG9ydDtUaGVzc2Fsb25pa2kgLSBUaGVzc2Fsb25pa2kgTWFjZWRvbmlhIEludGVybmF0aW9uYWwgQWlycG9ydCREdWJsaW4gLSBEdWJsaW4gQWlycG9ydCAtIFRlcm1pbmFsIDEqVGVsIEF2aXYgLSBCZW4gR3VyaW9uIEFpcnBvcnQgLSBUZXJtaW5hbCAzGkFsZ2hlcm8gLSBBbGdoZXJvIEZlcnRpbGlhE0JhcmkgLSBCYXJpIEFpcnBvcnQRQm9sb2duYSAtIE1hcmNvbmkWQ2F0YW5pYSAtIEZvbnRhbmFyb3NzYRNGbG9yZW5jZSAtIFBlcmV0b2xhHUxhbWV6aWEgVGVybWUgLSBMYW1lemlhIFRlcm1lH01pbGFuIChCZXJnYW1vKSAtIE9yaW8gYWwgU2VyaW8XTWlsYW4gKExpbmF0ZSkgLSBMaW5hdGURTWlsYW5vIC0gTWFscGVuc2EXTmFwbGVzIC0gTmFwb2xpIEFpcnBvcnQRUGVzY2FyYSAtIEFicnV6em8bUm9tZSAtIEZpdW1pY2lubyBUZXJtaW5hbCAyD1R1cmluIC0gQ2FzZWxsZSRMaXNib24gLSBMaXNib24gQWlycG9ydCAoVGVybWluYWwgMikdQmFjYXUgLSBHZW9yZ2UgRW5lc2N1IEFpcnBvcnQZQnJhc292IC0gdHJhbnNmZXIgLSBjb2FjaBNCdWNoYXJlc3QgLSBPdG9wZW5pIUNsdWotTmFwb2NhIC0gQXZyYW0gSWFuY3UgQWlycG9ydB9Db25zdGFudGEgLSBNaWhhaWwgS29nYWxuaWNlYW51HENvbnN0YW50YSAtIHRyYW5zZmVyIC0gY29hY2gTSWFzaSAtIElhc2kgQWlycG9ydA9PcmFkZWEgLSBPcmFkZWENU2liaXUgLSBTaWJpdRdUaW1pc29hcmEgLSBUcmFpYW4gVnVpYRtBbGljYW50ZSAtIEFMSUNBTlRFIEFJUlBPUlQgQmFyY2Vsb25hIC0gRWwgUHJhdCAtIFRlcm1pbmFsIDIpQ2FzdGVsbG9uIC0gQ2FzdGVsbG9uIERlIExhIFBsYW5hIEFpcnBvcnQNSWJpemEgLSBJYml6YRtNYWRyaWQgLSBCYXJhamFzIFRlcm1pbmFsIDElTWFsYWdhICAtIE1hbGFnYSBBaXJwb3J0IC0gVGVybWluYWwgMi1QYWxtYSBkZSBNYWxsb3JjYSAtIFBhbG1hIGRlIE1hbGxvcmNhIEFpcnBvcnQZU2V2aWxsZSAtIFNldmlsbGEgQWlycG9ydB9WYWxlbmNpYSAtIE1hbmlzZXMgLSBUZXJtaW5hbCAyMlN0b2NraG9sbSAtIFN0b2NraG9sbSBBcmxhbmRhIEFpcnBvcnQgKFRlcm1pbmFsIDUpFTYCLTEDQlJVA0xDQQNDUEgDQk9EA0xZUwNOQ0UDQlZBA1RYTANIQU0DQ0dOA1NUUgNCSFgDR0xBA0xQTANMVE4DQVRIA1NLRwNEVUIDVExWA0FITwNCUkkDQkxRA0NUQQNGTFIDU1VGA0JHWQNMSU4DTVhQA05BUANQU1IDRkNPA1RSTgNMSVMDQkNNA1hIVgNPVFADQ0xKA0NNRANDTkQDSUFTA09NUgNTQloDVFNSA0FMQwNCQ04DQ0RUA0lCWgNNQUQDQUdQA1BNSQNTVlEDVkxDA0FSThQrAzZnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2cWAWZkAgcPEA8WBh8HBQRJQVRBHwgFBE5hbWUfCWdkEBU2CVNlbGVjdC4uLhpBbGdoZXJvIC0gQWxnaGVybyBGZXJ0aWxpYRtBbGljYW50ZSAtIEFMSUNBTlRFIEFJUlBPUlQlQXRoZW5zIC0gQXRoZW5zIEludGVybmF0aW9uYWwgQWlycG9ydB1CYWNhdSAtIEdlb3JnZSBFbmVzY3UgQWlycG9ydCBCYXJjZWxvbmEgLSBFbCBQcmF0IC0gVGVybWluYWwgMhNCYXJpIC0gQmFyaSBBaXJwb3J0DkJlcmxpbiAtIFRlZ2VsH0Jpcm1pbmdoYW0gLSBCaXJtaW5naGFtIEFpcnBvcnQRQm9sb2duYSAtIE1hcmNvbmkbQm9yZGVhdXggLSBCb3JkZWF1eCBBaXJwb3J0GUJyYXNvdiAtIHRyYW5zZmVyIC0gY29hY2gpQnJ1c3NlbHMgLSBCcnV4ZWxsZXMgQWlycG9ydCAtIFRlcm1pbmFsIEITQnVjaGFyZXN0IC0gT3RvcGVuaSlDYXN0ZWxsb24gLSBDYXN0ZWxsb24gRGUgTGEgUGxhbmEgQWlycG9ydBZDYXRhbmlhIC0gRm9udGFuYXJvc3NhIUNsdWotTmFwb2NhIC0gQXZyYW0gSWFuY3UgQWlycG9ydB9Db25zdGFudGEgLSBNaWhhaWwgS29nYWxuaWNlYW51HENvbnN0YW50YSAtIHRyYW5zZmVyIC0gY29hY2gfQ29wZW5oYWdlbiAtIENvcGVuaGFnZW4gQWlycG9ydCREdWJsaW4gLSBEdWJsaW4gQWlycG9ydCAtIFRlcm1pbmFsIDETRmxvcmVuY2UgLSBQZXJldG9sYTRHbGFzZ293IC0gR2xhc2dvdyBJbnRlcm5hdGlvbmFsIEFpcnBvcnQgKFRlcm1pbmFsIDEpJkhhbWJ1cmcgLSBIYW1idXJnIEFpcnBvcnQgKFRlcm1pbmFsIDIpE0lhc2kgLSBJYXNpIEFpcnBvcnQNSWJpemEgLSBJYml6YR1Lb2xuIC0gS29sbiBCb25uIC0gVGVybWluYWwgMh1MYW1lemlhIFRlcm1lIC0gTGFtZXppYSBUZXJtZRlMYXJuYWNhIC0gTGFybmFjYSBBaXJwb3J0JExpc2JvbiAtIExpc2JvbiBBaXJwb3J0IChUZXJtaW5hbCAyKR9MaXZlcnBvb2wgLSBKb2huIExlbm5vbiBBaXJwb3J0FkxvbmRvbiAoTHV0b24pIC0gTHV0b24fTHlvbiAtIFN0LiBFeHVwZXJ5IChUZXJtaW5hbCAxKRtNYWRyaWQgLSBCYXJhamFzIFRlcm1pbmFsIDElTWFsYWdhICAtIE1hbGFnYSBBaXJwb3J0IC0gVGVybWluYWwgMh9NaWxhbiAoQmVyZ2FtbykgLSBPcmlvIGFsIFNlcmlvF01pbGFuIChMaW5hdGUpIC0gTGluYXRlEU1pbGFubyAtIE1hbHBlbnNhF05hcGxlcyAtIE5hcG9saSBBaXJwb3J0IE5pY2UgLSBDw7R0ZSBkJ0F6dXIgLSBUZXJtaW5hbCAxD09yYWRlYSAtIE9yYWRlYS1QYWxtYSBkZSBNYWxsb3JjYSAtIFBhbG1hIGRlIE1hbGxvcmNhIEFpcnBvcnQbUGFyaXMgKEJlYXV2YWlzKSAtIEJlYXV2YWlzEVBlc2NhcmEgLSBBYnJ1enpvG1JvbWUgLSBGaXVtaWNpbm8gVGVybWluYWwgMhlTZXZpbGxlIC0gU2V2aWxsYSBBaXJwb3J0DVNpYml1IC0gU2liaXUyU3RvY2tob2xtIC0gU3RvY2tob2xtIEFybGFuZGEgQWlycG9ydCAoVGVybWluYWwgNSkqU3R1dHRnYXJ0IC0gU3R1dHRnYXJ0IEFpcnBvcnQgKFRlcm1pbmFsIDMpKlRlbCBBdml2IC0gQmVuIEd1cmlvbiBBaXJwb3J0IC0gVGVybWluYWwgMztUaGVzc2Fsb25pa2kgLSBUaGVzc2Fsb25pa2kgTWFjZWRvbmlhIEludGVybmF0aW9uYWwgQWlycG9ydBdUaW1pc29hcmEgLSBUcmFpYW4gVnVpYQ9UdXJpbiAtIENhc2VsbGUfVmFsZW5jaWEgLSBNYW5pc2VzIC0gVGVybWluYWwgMhU2Ai0xA0FITwNBTEMDQVRIA0JDTQNCQ04DQlJJA1RYTANCSFgDQkxRA0JPRANYSFYDQlJVA09UUANDRFQDQ1RBA0NMSgNDTUQDQ05EA0NQSANEVUIDRkxSA0dMQQNIQU0DSUFTA0lCWgNDR04DU1VGA0xDQQNMSVMDTFBMA0xUTgNMWVMDTUFEA0FHUANCR1kDTElOA01YUANOQVADTkNFA09NUgNQTUkDQlZBA1BTUgNGQ08DU1ZRA1NCWgNBUk4DU1RSA1RMVgNTS0cDVFNSA1RSTgNWTEMUKwM2Z2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnFgFmZAIMDw9kFgIeBXN0eWxlBQ5kaXNwbGF5OmJsb2NrO2QCDQ8QZA8WCmYCAQICAgMCBAIFAgYCBwIIAgkWChAFATEFATFnEAUBMgUBMmcQBQEzBQEzZxAFATQFATRnEAUBNQUBNWcQBQE2BQE2ZxAFATcFATdnEAUBOAUBOGcQBQE5BQE5ZxAFAjEwBQIxMGdkZAIODxBkDxYGZgIBAgICAwIEAgUWBhAFATAFATBnEAUBMQUBMWcQBQEyBQEyZxAFATMFATNnEAUBNAUBNGcQBQE1BQE1Z2RkAhAPEGQPFgZmAgECAgIDAgQCBRYGEAUBMAUBMGcQBQExBQExZxAFATIFATJnEAUBMwUBM2cQBQE0BQE0ZxAFATUFATVnZGQCEw8PFgIfAwUvaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9UZXJtcy1BbmQtQ29uZGl0aW9ucy9kZAIbDw8WAh8DBS1odHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL29ubGluZS9Ib3RlbC1Ib3N0ZWxkZAIcDw8WAh8DBTFodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL29ubGluZS9DYXItUmVzZXJ2YXRpb25zZGQCHQ8PFgIfAwUgaHR0cDovL2JsdWVhaXIucGFya3ZpYS5jb20vZW4tR0JkZAIFD2QWAmYPZBYCAgEPDxYCHgdWaXNpYmxlaGRkAgMPZBYCZg9kFhACAQ9kFgZmDw8WBB4IQ3NzQ2xhc3MFFUJvb2tpbmdIZWFkZXJJbWFnZSBpMB4EXyFTQgICZGQCAg8PFgIeBFRleHQFDVNlYXJjaCBmbGlnaHRkZAIED2QWDAIBDw8WBh8MBQ90YWIgaTAgc2VsZWN0ZWQeDU9uQ2xpZW50Q2xpY2sFDXJldHVybiBmYWxzZTsfDQICZGQCAw8PFgIfDwUNcmV0dXJuIGZhbHNlO2RkAgUPDxYCHw8FDXJldHVybiBmYWxzZTtkZAIHDw8WAh8PBQ1yZXR1cm4gZmFsc2U7ZGQCCQ8PFgIfDwUNcmV0dXJuIGZhbHNlO2RkAgsPDxYCHw8FDXJldHVybiBmYWxzZTtkZAILDxAPFgYfBwUESUFUQR8IBQROYW1lHwlnZBAVNglTZWxlY3QuLi4aQWxnaGVybyAtIEFsZ2hlcm8gRmVydGlsaWEbQWxpY2FudGUgLSBBTElDQU5URSBBSVJQT1JUJUF0aGVucyAtIEF0aGVucyBJbnRlcm5hdGlvbmFsIEFpcnBvcnQdQmFjYXUgLSBHZW9yZ2UgRW5lc2N1IEFpcnBvcnQgQmFyY2Vsb25hIC0gRWwgUHJhdCAtIFRlcm1pbmFsIDITQmFyaSAtIEJhcmkgQWlycG9ydA5CZXJsaW4gLSBUZWdlbB9CaXJtaW5naGFtIC0gQmlybWluZ2hhbSBBaXJwb3J0EUJvbG9nbmEgLSBNYXJjb25pG0JvcmRlYXV4IC0gQm9yZGVhdXggQWlycG9ydBlCcmFzb3YgLSB0cmFuc2ZlciAtIGNvYWNoKUJydXNzZWxzIC0gQnJ1eGVsbGVzIEFpcnBvcnQgLSBUZXJtaW5hbCBCE0J1Y2hhcmVzdCAtIE90b3BlbmkpQ2FzdGVsbG9uIC0gQ2FzdGVsbG9uIERlIExhIFBsYW5hIEFpcnBvcnQWQ2F0YW5pYSAtIEZvbnRhbmFyb3NzYSFDbHVqLU5hcG9jYSAtIEF2cmFtIElhbmN1IEFpcnBvcnQfQ29uc3RhbnRhIC0gTWloYWlsIEtvZ2FsbmljZWFudRxDb25zdGFudGEgLSB0cmFuc2ZlciAtIGNvYWNoH0NvcGVuaGFnZW4gLSBDb3BlbmhhZ2VuIEFpcnBvcnQkRHVibGluIC0gRHVibGluIEFpcnBvcnQgLSBUZXJtaW5hbCAxE0Zsb3JlbmNlIC0gUGVyZXRvbGE0R2xhc2dvdyAtIEdsYXNnb3cgSW50ZXJuYXRpb25hbCBBaXJwb3J0IChUZXJtaW5hbCAxKSZIYW1idXJnIC0gSGFtYnVyZyBBaXJwb3J0IChUZXJtaW5hbCAyKRNJYXNpIC0gSWFzaSBBaXJwb3J0DUliaXphIC0gSWJpemEdS29sbiAtIEtvbG4gQm9ubiAtIFRlcm1pbmFsIDIdTGFtZXppYSBUZXJtZSAtIExhbWV6aWEgVGVybWUZTGFybmFjYSAtIExhcm5hY2EgQWlycG9ydCRMaXNib24gLSBMaXNib24gQWlycG9ydCAoVGVybWluYWwgMikfTGl2ZXJwb29sIC0gSm9obiBMZW5ub24gQWlycG9ydBZMb25kb24gKEx1dG9uKSAtIEx1dG9uH0x5b24gLSBTdC4gRXh1cGVyeSAoVGVybWluYWwgMSkbTWFkcmlkIC0gQmFyYWphcyBUZXJtaW5hbCAxJU1hbGFnYSAgLSBNYWxhZ2EgQWlycG9ydCAtIFRlcm1pbmFsIDIfTWlsYW4gKEJlcmdhbW8pIC0gT3JpbyBhbCBTZXJpbxdNaWxhbiAoTGluYXRlKSAtIExpbmF0ZRFNaWxhbm8gLSBNYWxwZW5zYRdOYXBsZXMgLSBOYXBvbGkgQWlycG9ydCBOaWNlIC0gQ8O0dGUgZCdBenVyIC0gVGVybWluYWwgMQ9PcmFkZWEgLSBPcmFkZWEtUGFsbWEgZGUgTWFsbG9yY2EgLSBQYWxtYSBkZSBNYWxsb3JjYSBBaXJwb3J0G1BhcmlzIChCZWF1dmFpcykgLSBCZWF1dmFpcxFQZXNjYXJhIC0gQWJydXp6bxtSb21lIC0gRml1bWljaW5vIFRlcm1pbmFsIDIZU2V2aWxsZSAtIFNldmlsbGEgQWlycG9ydA1TaWJpdSAtIFNpYml1MlN0b2NraG9sbSAtIFN0b2NraG9sbSBBcmxhbmRhIEFpcnBvcnQgKFRlcm1pbmFsIDUpKlN0dXR0Z2FydCAtIFN0dXR0Z2FydCBBaXJwb3J0IChUZXJtaW5hbCAzKSpUZWwgQXZpdiAtIEJlbiBHdXJpb24gQWlycG9ydCAtIFRlcm1pbmFsIDM7VGhlc3NhbG9uaWtpIC0gVGhlc3NhbG9uaWtpIE1hY2Vkb25pYSBJbnRlcm5hdGlvbmFsIEFpcnBvcnQXVGltaXNvYXJhIC0gVHJhaWFuIFZ1aWEPVHVyaW4gLSBDYXNlbGxlH1ZhbGVuY2lhIC0gTWFuaXNlcyAtIFRlcm1pbmFsIDIVNgItMQNBSE8DQUxDA0FUSANCQ00DQkNOA0JSSQNUWEwDQkhYA0JMUQNCT0QDWEhWA0JSVQNPVFADQ0RUA0NUQQNDTEoDQ01EA0NORANDUEgDRFVCA0ZMUgNHTEEDSEFNA0lBUwNJQloDQ0dOA1NVRgNMQ0EDTElTA0xQTANMVE4DTFlTA01BRANBR1ADQkdZA0xJTgNNWFADTkFQA05DRQNPTVIDUE1JA0JWQQNQU1IDRkNPA1NWUQNTQloDQVJOA1NUUgNUTFYDU0tHA1RTUgNUUk4DVkxDFCsDNmdnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZxYBZmQCFQ8QDxYGHwcFBElBVEEfCAUETmFtZR8JZ2QQFTYJU2VsZWN0Li4uGkFsZ2hlcm8gLSBBbGdoZXJvIEZlcnRpbGlhG0FsaWNhbnRlIC0gQUxJQ0FOVEUgQUlSUE9SVCVBdGhlbnMgLSBBdGhlbnMgSW50ZXJuYXRpb25hbCBBaXJwb3J0HUJhY2F1IC0gR2VvcmdlIEVuZXNjdSBBaXJwb3J0IEJhcmNlbG9uYSAtIEVsIFByYXQgLSBUZXJtaW5hbCAyE0JhcmkgLSBCYXJpIEFpcnBvcnQOQmVybGluIC0gVGVnZWwfQmlybWluZ2hhbSAtIEJpcm1pbmdoYW0gQWlycG9ydBFCb2xvZ25hIC0gTWFyY29uaRtCb3JkZWF1eCAtIEJvcmRlYXV4IEFpcnBvcnQZQnJhc292IC0gdHJhbnNmZXIgLSBjb2FjaClCcnVzc2VscyAtIEJydXhlbGxlcyBBaXJwb3J0IC0gVGVybWluYWwgQhNCdWNoYXJlc3QgLSBPdG9wZW5pKUNhc3RlbGxvbiAtIENhc3RlbGxvbiBEZSBMYSBQbGFuYSBBaXJwb3J0FkNhdGFuaWEgLSBGb250YW5hcm9zc2EhQ2x1ai1OYXBvY2EgLSBBdnJhbSBJYW5jdSBBaXJwb3J0H0NvbnN0YW50YSAtIE1paGFpbCBLb2dhbG5pY2VhbnUcQ29uc3RhbnRhIC0gdHJhbnNmZXIgLSBjb2FjaB9Db3BlbmhhZ2VuIC0gQ29wZW5oYWdlbiBBaXJwb3J0JER1YmxpbiAtIER1YmxpbiBBaXJwb3J0IC0gVGVybWluYWwgMRNGbG9yZW5jZSAtIFBlcmV0b2xhNEdsYXNnb3cgLSBHbGFzZ293IEludGVybmF0aW9uYWwgQWlycG9ydCAoVGVybWluYWwgMSkmSGFtYnVyZyAtIEhhbWJ1cmcgQWlycG9ydCAoVGVybWluYWwgMikTSWFzaSAtIElhc2kgQWlycG9ydA1JYml6YSAtIEliaXphHUtvbG4gLSBLb2xuIEJvbm4gLSBUZXJtaW5hbCAyHUxhbWV6aWEgVGVybWUgLSBMYW1lemlhIFRlcm1lGUxhcm5hY2EgLSBMYXJuYWNhIEFpcnBvcnQkTGlzYm9uIC0gTGlzYm9uIEFpcnBvcnQgKFRlcm1pbmFsIDIpH0xpdmVycG9vbCAtIEpvaG4gTGVubm9uIEFpcnBvcnQWTG9uZG9uIChMdXRvbikgLSBMdXRvbh9MeW9uIC0gU3QuIEV4dXBlcnkgKFRlcm1pbmFsIDEpG01hZHJpZCAtIEJhcmFqYXMgVGVybWluYWwgMSVNYWxhZ2EgIC0gTWFsYWdhIEFpcnBvcnQgLSBUZXJtaW5hbCAyH01pbGFuIChCZXJnYW1vKSAtIE9yaW8gYWwgU2VyaW8XTWlsYW4gKExpbmF0ZSkgLSBMaW5hdGURTWlsYW5vIC0gTWFscGVuc2EXTmFwbGVzIC0gTmFwb2xpIEFpcnBvcnQgTmljZSAtIEPDtHRlIGQnQXp1ciAtIFRlcm1pbmFsIDEPT3JhZGVhIC0gT3JhZGVhLVBhbG1hIGRlIE1hbGxvcmNhIC0gUGFsbWEgZGUgTWFsbG9yY2EgQWlycG9ydBtQYXJpcyAoQmVhdXZhaXMpIC0gQmVhdXZhaXMRUGVzY2FyYSAtIEFicnV6em8bUm9tZSAtIEZpdW1pY2lubyBUZXJtaW5hbCAyGVNldmlsbGUgLSBTZXZpbGxhIEFpcnBvcnQNU2liaXUgLSBTaWJpdTJTdG9ja2hvbG0gLSBTdG9ja2hvbG0gQXJsYW5kYSBBaXJwb3J0IChUZXJtaW5hbCA1KSpTdHV0dGdhcnQgLSBTdHV0dGdhcnQgQWlycG9ydCAoVGVybWluYWwgMykqVGVsIEF2aXYgLSBCZW4gR3VyaW9uIEFpcnBvcnQgLSBUZXJtaW5hbCAzO1RoZXNzYWxvbmlraSAtIFRoZXNzYWxvbmlraSBNYWNlZG9uaWEgSW50ZXJuYXRpb25hbCBBaXJwb3J0F1RpbWlzb2FyYSAtIFRyYWlhbiBWdWlhD1R1cmluIC0gQ2FzZWxsZR9WYWxlbmNpYSAtIE1hbmlzZXMgLSBUZXJtaW5hbCAyFTYCLTEDQUhPA0FMQwNBVEgDQkNNA0JDTgNCUkkDVFhMA0JIWANCTFEDQk9EA1hIVgNCUlUDT1RQA0NEVANDVEEDQ0xKA0NNRANDTkQDQ1BIA0RVQgNGTFIDR0xBA0hBTQNJQVMDSUJaA0NHTgNTVUYDTENBA0xJUwNMUEwDTFROA0xZUwNNQUQDQUdQA0JHWQNMSU4DTVhQA05BUANOQ0UDT01SA1BNSQNCVkEDUFNSA0ZDTwNTVlEDU0JaA0FSTgNTVFIDVExWA1NLRwNUU1IDVFJOA1ZMQxQrAzZnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2cWAWZkAisPEGQPFgpmAgECAgIDAgQCBQIGAgcCCAIJFgoQBQExBQExZxAFATIFATJnEAUBMwUBM2cQBQE0BQE0ZxAFATUFATVnEAUBNgUBNmcQBQE3BQE3ZxAFATgFAThnEAUBOQUBOWcQBQIxMAUCMTBnZGQCLw8QZA8WBmYCAQICAgMCBAIFFgYQBQEwBQEwZxAFATEFATFnEAUBMgUBMmcQBQEzBQEzZxAFATQFATRnEAUBNQUBNWdkZAI1DxBkDxYGZgIBAgICAwIEAgUWBhAFATAFATBnEAUBMQUBMWcQBQEyBQEyZxAFATMFATNnEAUBNAUBNGcQBQE1BQE1Z2RkAj0PDxYCHwMFL2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVGVybXMtQW5kLUNvbmRpdGlvbnMvZGQCRQ9kFgRmDw8WAh8OBc4CPHA+V2UgbGlrZSBvdXIgY3VzdG9tZXJzIHRvIGJlIGluZm9ybWVkLiBUaGVyZWZvcmUsIHJlZ2FyZGxlc3Mgb2YgdGhlIGRvdWJ0IHlvdSBoYXZlLCB5b3UgY2FuIGNvbnRhY3Qgb3VyIENhbGwgQ2VudGVyIGNvbnN1bHRhbnRzPHN0cm9uZz4sIDwvc3Ryb25nPm51bWJlcnMgYXJlIGF2YWlsYWJsZSA8YSBocmVmPSIuLi8uLi9VdGlsLUluZm9zL0NhbGwtQ2VudGVyIj5oZXJlPC9hPi48L3A+PHA+WW91IGNhbiBhbHdheXMmbmJzcDtjb250YWN0IHVzIGZyZWUgb2YgY2hhcmdlIGJ5Jm5ic3A7PGEgaHJlZj0iLi4vLi4vQ29udGFjdC8iPnN1Ym1pdHRpbmcgYSBxdWVzdGlvbjwvYT4uPC9wPmRkAgIPDxYCHw4F8hM8cD5UcmFuc2FjdGlvbnMgZWZmZWN0ZWQgb24gb3Blbi5tYXhpdG91cnMuYmUgaW4gRVVSLCBVU0QsIEdCUCBhcmUgcmVuZGVyZWQgc2VjdXJlIGJ5IHRoZSBJbmdlbmljbyBwYXltZW50IHNlcnZpY2VzICg8YSBocmVmPSJodHRwOi8vd3d3LnBheW1lbnQtc2VydmljZXMuaW5nZW5pY28uY29tLyIgdGFyZ2V0PSJfYmxhbmsiPjxzdHJvbmc+d3d3LnBheW1lbnQtc2VydmljZXMuaW5nZW5pY28uY29tPC9zdHJvbmc+PC9hPikuIEFsbCBpbmZvcm1hdGlvbiBleGNoYW5nZWQgdG8gcHJvY2VzcyB0aGUgcGF5bWVudCBpcyBlbmNyeXB0ZWQgdXNpbmcgdGhlIFNTTCBwcm90b2NvbC4gVGhlc2UgZGF0YSBjYW5ub3QgYmUgZGV0ZWN0ZWQsIGludGVyY2VwdGVkIG9yIHVzZWQgYnkgdGhpcmQgcGFydGllcywgYW5kIGFyZSBub3Qga2VwdCBvbiBvdXIgY29tcHV0ZXIgc3lzdGVtcyBlaXRoZXIuPGJyIC8+SW5nZW5pY28gaXMgYSB0ZWNobmljYWwgc2VydmljZSBwcm92aWRlciwgYW5kIGRvZXMgbm90IHRha2UgY2FyZSBvZiBkaXNwdXRlcyBsaW5rZWQgdG8gdGhlIG9yZGVycy4gVGhlc2Ugc2hvdWxkIGJlIHNldHRsZWQgZGlyZWN0bHkgd2l0aCBvcGVuLm1heGl0b3Vycy5iZSBhbmQvb3IgeW91ciBiYW5rLjxiciAvPk1vcmUgaW5mb3JtYXRpb24gYWJvdXQgSW5nZW5pY286Jm5ic3A7PGEgaHJlZj0iaHR0cDovL3d3dy5wYXltZW50LXNlcnZpY2VzLmluZ2VuaWNvLmNvbS8iIHRhcmdldD0iX2JsYW5rIj48c3Ryb25nPnd3dy5wYXltZW50LXNlcnZpY2VzLmluZ2VuaWNvLmNvbTwvc3Ryb25nPjwvYT48L3A+PHA+Jm5ic3A7PC9wPjxwPjxpbWcgc3R5bGU9ImZsb2F0OiBsZWZ0OyBoZWlnaHQ6IDE1cHg7IG1hcmdpbi1yaWdodDogMTBweDsiIHNyYz0iaW1hZ2VzL2xvZ29fcGF5dS5wbmciIGJvcmRlcj0iMCIgYWx0PSIiIC8+VHJhbnNhY3Rpb25zIGVmZmVjdGVkIG9uIG9wZW4ubWF4aXRvdXJzLmJlIGluIFJPTiBhcmUgcmVuZGVyZWQgc2VjdXJlIGJ5IHRoZSBQYXlVIHBheW1lbnQgc2VydmljZXMgKDxhIGhyZWY9Imh0dHA6Ly93d3cucGF5dS5ybyIgdGFyZ2V0PSJfYmxhbmsiPjxzdHJvbmc+d3d3LnBheXUucm88L3N0cm9uZz48L2E+KS4gQWxsIGluZm9ybWF0aW9uIGV4Y2hhbmdlZCB0byBwcm9jZXNzIHRoZSBwYXltZW50IGlzIGVuY3J5cHRlZCB1c2luZyB0aGUgU1NMIHByb3RvY29sLiBUaGVzZSBkYXRhIGNhbm5vdCBiZSBkZXRlY3RlZCwgaW50ZXJjZXB0ZWQgb3IgdXNlZCBieSB0aGlyZCBwYXJ0aWVzLCBhbmQgYXJlIG5vdCBrZXB0IG9uIG91ciBjb21wdXRlciBzeXN0ZW1zIGVpdGhlci48YnIgLz5QYXlVIGlzIGEgdGVjaG5pY2FsIHNlcnZpY2UgcHJvdmlkZXIsIGFuZCBkb2VzIG5vdCB0YWtlIGNhcmUgb2YgZGlzcHV0ZXMgbGlua2VkIHRvIHRoZSBvcmRlcnMuIFRoZXNlIHNob3VsZCBiZSBzZXR0bGVkIGRpcmVjdGx5IHdpdGggb3Blbi5tYXhpdG91cnMuYmUgYW5kL29yIHlvdXIgYmFuay48YnIgLz5Nb3JlIGluZm9ybWF0aW9uIGFib3V0IFBheVU6Jm5ic3A7PGEgaHJlZj0iaHR0cDovL3d3dy5wYXl1LnJvIiB0YXJnZXQ9Il9ibGFuayI+PHN0cm9uZz53d3cucGF5dS5ybzwvc3Ryb25nPjwvYT4uPC9wPjxwPiZuYnNwOzwvcD48cD48aW1nIHN0eWxlPSJmbG9hdDogbGVmdDsgaGVpZ2h0OiAxNXB4OyBtYXJnaW4tcmlnaHQ6IDEwcHg7IiBzcmM9ImltYWdlcy9sb2dvX3BheXBhbC5wbmciIGJvcmRlcj0iMCIgYWx0PSIiIC8+VHJhbnNhY3Rpb25zIGVmZmVjdGVkIG9uIG9wZW4ubWF4aXRvdXJzLmJlIGluIEVVUiwgVVNELCBHQlAgYXJlIHJlbmRlcmVkIHNlY3VyZSBieSB0aGUgUGF5UGFsIHBheW1lbnQgc2VydmljZXMgKDxhIGhyZWY9Imh0dHBzOi8vd3d3LnBheXBhbC5jb20vdXMvd2ViYXBwcy9tcHAvcGF5cGFsLXBvcHVwIj48c3Ryb25nPmh0dHBzOi8vd3d3LnBheXBhbC5jb20vdXMvd2ViYXBwcy9tcHAvcGF5cGFsLXBvcHVwPC9zdHJvbmc+PC9hPikgUGF5bWVudHMgd2l0aCBWaXNhIGFuZCBNYXN0ZXJjYXJkIGFyZSBhY2NlcHRlZCwgbm90IHdpdGggQW1lcmljYW4gRXhwcmVzcy4gQWxsIGluZm9ybWF0aW9uIGV4Y2hhbmdlZCB0byBwcm9jZXNzIHRoZSBwYXltZW50IGlzIGVuY3J5cHRlZCB1c2luZyB0aGUgU1NMIHByb3RvY29sLiBUaGVzZSBkYXRhIGNhbm5vdCBiZSBkZXRlY3RlZCwgaW50ZXJjZXB0ZWQgb3IgdXNlZCBieSB0aGlyZCBwYXJ0aWVzLCBhbmQgYXJlIG5vdCBrZXB0IG9uIG91ciBjb21wdXRlciBzeXN0ZW1zIGVpdGhlci48YnIgLz4gUGF5UGFsIGlzIGEgdGVjaG5pY2FsIHNlcnZpY2UgcHJvdmlkZXIsIGFuZCBkb2VzIG5vdCB0YWtlIGNhcmUgb2YgZGlzcHV0ZXMgbGlua2VkIHRvIHRoZSBvcmRlcnMuIFRoZXNlIHNob3VsZCBiZSBzZXR0bGVkIGRpcmVjdGx5IHdpdGggb3Blbi5tYXhpdG91cnMuYmUgYW5kL29yIHlvdXIgYmFuay48YnIgLz4gTW9yZSBpbmZvcm1hdGlvbiBhYm91dCBQYXlQYWw6Jm5ic3A7PGEgaHJlZj0iaHR0cHM6Ly93d3cucGF5cGFsLmNvbS8iPjxzdHJvbmc+aHR0cHM6Ly93d3cucGF5cGFsLmNvbS88L3N0cm9uZz48L2E+PC9wPmRkAgkPDxYCHwtoZGQCCw8PFgIfAwUuaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9DYXJyaWFnZS1Db25kaXRpb25zL2RkAgwPDxYCHwMFL2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVGVybXMtQW5kLUNvbmRpdGlvbnMvZGQCDQ8PFgIfAwUiaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9DYXJlZXJzL2RkAg4PDxYCHwMFKmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVXRpbC1JbmZvcy9QcmVzc2RkAg8PDxYCHwMFL2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vVXRpbC1JbmZvcy9OZXdzbGV0dGVyZGQCEA8PFgIfAwUiaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9Db250YWN0L2RkAhEPDxYCHwMFI2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vT3BpbmlvbnMvZGQCEg8PFgIfAwUjaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9TaXRlLU1hcC9kZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WBgUyY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRCb29raW5nQm94MSRyZG9Sb3VuZFRyaXAFL2N0bDAwJENvbnRlbnRQbGFjZUhvbGRlcjEkQm9va2luZ0JveDEkcmRvT25lV2F5BS9jdGwwMCRDb250ZW50UGxhY2VIb2xkZXIxJEJvb2tpbmdCb3gxJHJkb09uZVdheQUmY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRyZG9Sb3VuZFRyaXAFI2N0bDAwJENvbnRlbnRQbGFjZUhvbGRlcjEkcmRvT25lV2F5BSNjdGwwMCRDb250ZW50UGxhY2VIb2xkZXIxJHJkb09uZVdheY161P2DwJCw3dYHO4vThFgKy9RtFuAKUeSvUUhRLI1Z'
            ]
        ];

        $form->post   =   $query->getPost();    // + $form->post;
        return $form;
    }

    public function getFlights(Query $query)
    {
        $form   =   $this->createSearchPost($query);
        $redirect_response_src   =   $this->api($form)->getBody();

        $action =   $this->getRedirectForm($redirect_response_src);
        $raw_flightspage    =   $this->api($action)->getBody();

        $ctr    =   $this->getFlightSearchResults($raw_flightspage);

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
    public function getFlightSearchResults($src)
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
                    $flights->addFlight($direction, iterator_to_array($segments), [
                        // this is the total price for all passenger tickets
                        'price_allpassengers' =>  $xpath->query('a/div/span/s', $journeyitem)->item(0)->textContent
                    ]);
                }
//                $dateflights[]  =   1;
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