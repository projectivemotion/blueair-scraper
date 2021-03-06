<?php
/**
 * Project: BlueairScraper
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace projectivemotion\BlueairScraper\tests;


use projectivemotion\BlueairScraper\FlightsContainer;
use projectivemotion\BlueairScraper\Scraper;

class ParsingTest extends \PHPUnit_Framework_TestCase
{
    public static function readFile($file)
    {
        return file_get_contents(__DIR__ . '/' . $file);
    }

    public function testGetFormInputs()
    {
        $Scraper = new Scraper();
        $formreq =   $Scraper->getFormInputs(self::readFile('home.html'));
        $inputs = $formreq->post;

        $this->assertArrayHasKey('__VIEWSTATEGENERATOR', $inputs);
        $this->assertArrayHasKey('__EVENTVALIDATION', $inputs);
        $this->assertArrayHasKey('__VIEWSTATE', $inputs);
        $this->assertArrayHasKey('__EVENTARGUMENT', $inputs);
        $this->assertArrayHasKey('__EVENTTARGET', $inputs);

        $this->assertEquals("/wEPDwUKMTM1MjU5MjcxMg9kFgJmD2QWAgIBD2QWHAIBDw8WAh4LTmF2aWdhdGVVcmwFDn4vRGVmYXVsdC5hc3B4ZGQCBA9kFgJmD2QWAgIBDxYCHgtfIUl0ZW1Db3VudGZkAgcPZBZGAgEPDxYEHwAFJ2h0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vUHJpbWEtUGFnaW5hLx4GVGFyZ2V0BQRfdG9wZGQCBQ8PFgQfAAU4aHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9EZXN0aW5hdGlpL0hhcnRhLURlc3RpbmF0aWlsb3IfAgUEX3RvcGRkAgcPDxYEHwAFMWh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vRGVzdGluYXRpaS9PcmFyLURlLVpib3IfAgUEX3RvcGRkAgkPDxYEHwAFPmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vRGVzdGluYXRpaS9EZXN0aW5hdGlpLVNpLUFlcm9wb3J0dXJpHwIFBF90b3BkZAILDw8WAh8ABSNodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2lpL2RkAg0PDxYEHwAFMmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vU2VydmljaWkvTG91bmdlLVByb3RvY29sHwIFBF90b3BkZAIPDw8WBB8ABTFodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2lpL1JlemVydmFyaS1HcnVwHwIFBF90b3BkZAIRDw8WBB8ABSpodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2lpL0NoYXJ0ZXIfAgUEX3RvcGRkAhMPDxYCHwAFLmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vU2VydmljaWkvQmx1ZS1CaXN0cm9kZAIVDw8WBB8ABShodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2lpL0NhcmdvHwIFBF90b3BkZAIXDw8WBB8ABStodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2lpL0UtQm9yZGVyHwIFBF90b3BkZAIbDw8WBB8ABS5odHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2lpL1B1YmxpY2l0YXRlHwIFBF90b3BkZAIdDw8WAh8ABS1odHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2lpL0UtU2VydmljZXNkZAIfDw8WAh8ABTNodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1NlcnZpY2lpL1RyYW5zZmVyLUF1dG9jYXJkZAIhDw8WBB8ABSxodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0NvcnBvcmF0ZS9Db250LU5vdR8CBQRfdG9wZGQCIw8PFgQfAAUnaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9BZ2VudGllL0luZGV4HwIFBF90b3BkZAInDw8WBB8ABTtodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0luZm9ybWF0aWktVXRpbGUvVWx0aW1lbGUtTm91dGF0aR8CBQRfdG9wZGQCKQ8PFgQfAAU4aHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9JbmZvcm1hdGlpLVV0aWxlL1ZvdWNoZXItQ2Fkb3UfAgUEX3RvcGRkAisPDxYEHwAFKWh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vSXN0b3JpYy1PZmVydGUvHwIFBF90b3BkZAItDw8WBB8ABTxodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0luZm9ybWF0aWktVXRpbGUvR2hpZC1EZS1DYWxhdG9yaWUfAgUEX3RvcGRkAi8PDxYEHwAFMGh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vQ29uZGl0aWktRGUtQ2FsYXRvcmllLx8CBQRfdG9wZGQCMQ8PFgQfAAU2aHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9JbmZvcm1hdGlpLVV0aWxlL0NhbGwtQ2VudGVyHwIFBF90b3BkZAIzDw8WBB8ABTVodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0luZm9ybWF0aWktVXRpbGUvTmV3c2xldHRlch8CBQRfdG9wZGQCNQ8PFgQfAAU9aHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9JbmZvcm1hdGlpLVV0aWxlL0FzaXN0ZW50YS1zcGVjaWFsYR8CBQRfdG9wZGQCOQ8PFgQfAAUtaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9Db21wYW5pZS9EZXNwcmUtTm9pHwIFBF90b3BkZAI7Dw8WBB8ABStodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0NvbXBhbmllL0VjaGlwYWplHwIFBF90b3BkZAI9Dw8WBB8ABShodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0NvbXBhbmllL0Zsb3RhHwIFBF90b3BkZAI/Dw8WBB8ABSxodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0NvbXBhbmllL1NpZ3VyYW50YR8CBQRfdG9wZGQCQQ8PFgQfAAUiaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9DYXJpZXJlLx8CBQRfdG9wZGQCQw8PFgQfAAU+aHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9JbmZvcm1hdGlpLVV0aWxlL0NvbXVuaWNhdGUtRGUtUHJlc2EfAgUEX3RvcGRkAkUPDxYEHwAFJWh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vQ29udGFjdC1yby8fAgUEX3RvcGRkAkcPDxYCHwAFKWh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vL0NvbXBhbmllL1N0YWZmZGQCSQ8PFgQfAAUzaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9Db21wYW5pZS9SZXZpc3RhLUluZmxpZ2h0HwIFBF90b3BkZAJLDw8WBB8CBQZfYmxhbmsfAAUgaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9yc3Mtcm9kZAJNDw8WAh8ABTdodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0luZm9ybWF0aWktVXRpbGUvQ2FzZXRhLVN0aXJpZGQCCA9kFgZmD2QWAmYPZBYEAgEPDxYEHg5GbGV4aWJsZURheXNObwICHhNSb3V0ZXNSb3VuZHRyaXBPbmx5BT9MQ0EtQkdXfEJHVy1MQ0F8TENBLUVCTHxFQkwtTENBfFRSTi1NQUh8TUFILVRSTnxUUk4tUE1JfFBNSS1UUk5kFhICAw8QDxYGHg5EYXRhVmFsdWVGaWVsZAUESUFUQR4NRGF0YVRleHRGaWVsZAUETmFtZR4LXyFEYXRhQm91bmRnZBAVNg1TZWxlY3RlYXphLi4uKkJydXhlbGxlcyAtIEJydXhlbGxlcyBBaXJwb3J0IC0gVGVybWluYWwgQhlMYXJuYWNhIC0gTGFybmFjYSBBaXJwb3J0HkNvcGVuaGFnYSAtIENvcGVuaGFnZW4gQWlycG9ydBtCb3JkZWF1eCAtIEJvcmRlYXV4IEFpcnBvcnQfTHlvbiAtIFN0LiBFeHVwZXJ5IChUZXJtaW5hbCAxKSBOaXNhIC0gQ8O0dGUgZCdBenVyIC0gVGVybWluYWwgMRtQYXJpcyAoQmVhdXZhaXMpIC0gQmVhdXZhaXMOQmVybGluIC0gVGVnZWwnSGFtYnVyZyAtIEFlcm9wb3J0IEhhbWJ1cmcgKFRlcm1pbmFsIDIpHUtvbG4gLSBLb2xuIEJvbm4gLSBUZXJtaW5hbCAyKlN0dXR0Z2FydCAtIFN0dXR0Z2FydCBBaXJwb3J0IChUZXJtaW5hbCAzKSRBdGVuYSAtIEF0aGVucyBJbnRlcm5hdGlvbmFsIEFpcnBvcnQ2U2Fsb25pYyAtIFRoZXNzYWxvbmlraSBNYWNlZG9uaWEgSW50ZXJuYXRpb25hbCBBaXJwb3J0JER1YmxpbiAtIER1YmxpbiBBaXJwb3J0IC0gVGVybWluYWwgMSpUZWwgQXZpdiAtIEJlbiBHdXJpb24gQWlycG9ydCAtIFRlcm1pbmFsIDMaQWxnaGVybyAtIEFsZ2hlcm8gRmVydGlsaWEUQmFyaSAtIEFlcm9wb3J0IEJhcmkRQm9sb2duYSAtIE1hcmNvbmkWQ2F0YW5pYSAtIEZvbnRhbmFyb3NzYRNGbG9yZW50YSAtIFBlcmV0b2xhKExhbWV6aWEgVGVybWUgLSBBZXJvcG9ydHVsIExhbWV6aWEgVGVybWURTWlsYW5vIC0gTWFscGVuc2EgTWlsYW5vIChCZXJnYW1vKSAtIE9yaW8gYWwgU2VyaW8YTWlsYW5vIChMaW5hdGUpIC0gTGluYXRlF05hcG9saSAtIE5hcG9saSBBaXJwb3J0EVBlc2NhcmEgLSBBYnJ1enpvG1JvbWEgLSBGaXVtaWNpbm8gVGVybWluYWwgMhBUb3Jpbm8gLSBDYXNlbGxlH0Jpcm1pbmdoYW0gLSBCaXJtaW5naGFtIEFpcnBvcnQ0R2xhc2dvdyAtIEdsYXNnb3cgSW50ZXJuYXRpb25hbCBBaXJwb3J0IChUZXJtaW5hbCAxKR9MaXZlcnBvb2wgLSBKb2huIExlbm5vbiBBaXJwb3J0FkxvbmRyYSAoTHV0b24pIC0gTHV0b24mTGlzYWJvbmEgLSBMaXNib24gQWlycG9ydCAoVGVybWluYWwgMikdQmFjYXUgLSBHZW9yZ2UgRW5lc2N1IEFpcnBvcnQbQnJhc292IC0gdHJhbnNmZXIgLSBhdXRvY2FyE0J1Y3VyZXN0aSAtIE90b3BlbmkhQ2x1ai1OYXBvY2EgLSBBdnJhbSBJYW5jdSBBaXJwb3J0H0NvbnN0YW50YSAtIE1paGFpbCBLb2dhbG5pY2VhbnUeQ29uc3RhbnRhIC0gdHJhbnNmZXIgLSBhdXRvY2FyFElhc2kgLSBBZXJvcG9ydCBJYXNpD09yYWRlYSAtIE9yYWRlYQ1TaWJpdSAtIFNpYml1F1RpbWlzb2FyYSAtIFRyYWlhbiBWdWlhG0FsaWNhbnRlIC0gQUxJQ0FOVEUgQUlSUE9SVCBCYXJjZWxvbmEgLSBFbCBQcmF0IC0gVGVybWluYWwgMilDYXN0ZWxsb24gLSBDYXN0ZWxsb24gRGUgTGEgUGxhbmEgQWlycG9ydA1JYml6YSAtIEliaXphG01hZHJpZCAtIEJhcmFqYXMgVGVybWluYWwgMSRNYWxhZ2EgLSBNYWxhZ2EgQWlycG9ydCAtIFRlcm1pbmFsIDItUGFsbWEgZGUgTWFsbG9yY2EgLSBQYWxtYSBkZSBNYWxsb3JjYSBBaXJwb3J0GVNldmlsbGEgLSBTZXZpbGxhIEFpcnBvcnQfVmFsZW5jaWEgLSBNYW5pc2VzIC0gVGVybWluYWwgMjJTdG9ja2hvbG0gLSBTdG9ja2hvbG0gQXJsYW5kYSBBaXJwb3J0IChUZXJtaW5hbCA1KRU2Ai0xA0JSVQNMQ0EDQ1BIA0JPRANMWVMDTkNFA0JWQQNUWEwDSEFNA0NHTgNTVFIDQVRIA1NLRwNEVUIDVExWA0FITwNCUkkDQkxRA0NUQQNGTFIDU1VGA01YUANCR1kDTElOA05BUANQU1IDRkNPA1RSTgNCSFgDR0xBA0xQTANMVE4DTElTA0JDTQNYSFYDT1RQA0NMSgNDTUQDQ05EA0lBUwNPTVIDU0JaA1RTUgNBTEMDQkNOA0NEVANJQloDTUFEA0FHUANQTUkDU1ZRA1ZMQwNBUk4UKwM2Z2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnFgFmZAIHDxAPFgYfBQUESUFUQR8GBQROYW1lHwdnZBAVNg1TZWxlY3RlYXphLi4uGkFsZ2hlcm8gLSBBbGdoZXJvIEZlcnRpbGlhG0FsaWNhbnRlIC0gQUxJQ0FOVEUgQUlSUE9SVCRBdGVuYSAtIEF0aGVucyBJbnRlcm5hdGlvbmFsIEFpcnBvcnQdQmFjYXUgLSBHZW9yZ2UgRW5lc2N1IEFpcnBvcnQgQmFyY2Vsb25hIC0gRWwgUHJhdCAtIFRlcm1pbmFsIDIUQmFyaSAtIEFlcm9wb3J0IEJhcmkOQmVybGluIC0gVGVnZWwfQmlybWluZ2hhbSAtIEJpcm1pbmdoYW0gQWlycG9ydBFCb2xvZ25hIC0gTWFyY29uaRtCb3JkZWF1eCAtIEJvcmRlYXV4IEFpcnBvcnQbQnJhc292IC0gdHJhbnNmZXIgLSBhdXRvY2FyKkJydXhlbGxlcyAtIEJydXhlbGxlcyBBaXJwb3J0IC0gVGVybWluYWwgQhNCdWN1cmVzdGkgLSBPdG9wZW5pKUNhc3RlbGxvbiAtIENhc3RlbGxvbiBEZSBMYSBQbGFuYSBBaXJwb3J0FkNhdGFuaWEgLSBGb250YW5hcm9zc2EhQ2x1ai1OYXBvY2EgLSBBdnJhbSBJYW5jdSBBaXJwb3J0H0NvbnN0YW50YSAtIE1paGFpbCBLb2dhbG5pY2VhbnUeQ29uc3RhbnRhIC0gdHJhbnNmZXIgLSBhdXRvY2FyHkNvcGVuaGFnYSAtIENvcGVuaGFnZW4gQWlycG9ydCREdWJsaW4gLSBEdWJsaW4gQWlycG9ydCAtIFRlcm1pbmFsIDETRmxvcmVudGEgLSBQZXJldG9sYTRHbGFzZ293IC0gR2xhc2dvdyBJbnRlcm5hdGlvbmFsIEFpcnBvcnQgKFRlcm1pbmFsIDEpJ0hhbWJ1cmcgLSBBZXJvcG9ydCBIYW1idXJnIChUZXJtaW5hbCAyKRRJYXNpIC0gQWVyb3BvcnQgSWFzaQ1JYml6YSAtIEliaXphHUtvbG4gLSBLb2xuIEJvbm4gLSBUZXJtaW5hbCAyKExhbWV6aWEgVGVybWUgLSBBZXJvcG9ydHVsIExhbWV6aWEgVGVybWUZTGFybmFjYSAtIExhcm5hY2EgQWlycG9ydCZMaXNhYm9uYSAtIExpc2JvbiBBaXJwb3J0IChUZXJtaW5hbCAyKR9MaXZlcnBvb2wgLSBKb2huIExlbm5vbiBBaXJwb3J0FkxvbmRyYSAoTHV0b24pIC0gTHV0b24fTHlvbiAtIFN0LiBFeHVwZXJ5IChUZXJtaW5hbCAxKRtNYWRyaWQgLSBCYXJhamFzIFRlcm1pbmFsIDEkTWFsYWdhIC0gTWFsYWdhIEFpcnBvcnQgLSBUZXJtaW5hbCAyEU1pbGFubyAtIE1hbHBlbnNhIE1pbGFubyAoQmVyZ2FtbykgLSBPcmlvIGFsIFNlcmlvGE1pbGFubyAoTGluYXRlKSAtIExpbmF0ZRdOYXBvbGkgLSBOYXBvbGkgQWlycG9ydCBOaXNhIC0gQ8O0dGUgZCdBenVyIC0gVGVybWluYWwgMQ9PcmFkZWEgLSBPcmFkZWEtUGFsbWEgZGUgTWFsbG9yY2EgLSBQYWxtYSBkZSBNYWxsb3JjYSBBaXJwb3J0G1BhcmlzIChCZWF1dmFpcykgLSBCZWF1dmFpcxFQZXNjYXJhIC0gQWJydXp6bxtSb21hIC0gRml1bWljaW5vIFRlcm1pbmFsIDI2U2Fsb25pYyAtIFRoZXNzYWxvbmlraSBNYWNlZG9uaWEgSW50ZXJuYXRpb25hbCBBaXJwb3J0GVNldmlsbGEgLSBTZXZpbGxhIEFpcnBvcnQNU2liaXUgLSBTaWJpdTJTdG9ja2hvbG0gLSBTdG9ja2hvbG0gQXJsYW5kYSBBaXJwb3J0IChUZXJtaW5hbCA1KSpTdHV0dGdhcnQgLSBTdHV0dGdhcnQgQWlycG9ydCAoVGVybWluYWwgMykqVGVsIEF2aXYgLSBCZW4gR3VyaW9uIEFpcnBvcnQgLSBUZXJtaW5hbCAzF1RpbWlzb2FyYSAtIFRyYWlhbiBWdWlhEFRvcmlubyAtIENhc2VsbGUfVmFsZW5jaWEgLSBNYW5pc2VzIC0gVGVybWluYWwgMhU2Ai0xA0FITwNBTEMDQVRIA0JDTQNCQ04DQlJJA1RYTANCSFgDQkxRA0JPRANYSFYDQlJVA09UUANDRFQDQ1RBA0NMSgNDTUQDQ05EA0NQSANEVUIDRkxSA0dMQQNIQU0DSUFTA0lCWgNDR04DU1VGA0xDQQNMSVMDTFBMA0xUTgNMWVMDTUFEA0FHUANNWFADQkdZA0xJTgNOQVADTkNFA09NUgNQTUkDQlZBA1BTUgNGQ08DU0tHA1NWUQNTQloDQVJOA1NUUgNUTFYDVFNSA1RSTgNWTEMUKwM2Z2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnFgFmZAINDxBkDxYKZgIBAgICAwIEAgUCBgIHAggCCRYKEAUBMQUBMWcQBQEyBQEyZxAFATMFATNnEAUBNAUBNGcQBQE1BQE1ZxAFATYFATZnEAUBNwUBN2cQBQE4BQE4ZxAFATkFATlnEAUCMTAFAjEwZ2RkAg4PEGQPFgZmAgECAgIDAgQCBRYGEAUBMAUBMGcQBQExBQExZxAFATIFATJnEAUBMwUBM2cQBQE0BQE0ZxAFATUFATVnZGQCEA8QZA8WBmYCAQICAgMCBAIFFgYQBQEwBQEwZxAFATEFATFnEAUBMgUBMmcQBQEzBQEzZxAFATQFATRnEAUBNQUBNWdkZAITDw8WAh8ABS5odHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1Rlcm1lbmktU2ktQ29uZGl0aWkvZGQCGw8PFgIfAAUwaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9PbmxpbmUtcm8vSG90ZWwtSG9zdGVsZGQCHA8PFgIfAAU0aHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9PbmxpbmUtcm8vUmV6ZXJ2YXJlLU1hc2luYWRkAh0PDxYCHwAFIGh0dHA6Ly9ibHVlYWlyLnBhcmt2aWEuY29tL3JvLVJPZGQCBQ9kFgRmD2QWAgIBDw8WAh4HVmlzaWJsZWhkZAIBDw8WAh8IaGRkAgEPZBYGZg8PFgQeCENzc0NsYXNzBRVCb29raW5nSGVhZGVySW1hZ2UgaTEeBF8hU0ICAmRkAgIPDxYCHgRUZXh0BQ9TZWxlY3RlYXphIHpib3JkZAIED2QWDAIBDw8WAh4NT25DbGllbnRDbGljawUNcmV0dXJuIGZhbHNlO2RkAgMPDxYGHwkFD3RhYiBpMSBzZWxlY3RlZB8MBQ1yZXR1cm4gZmFsc2U7HwoCAmRkAgUPDxYCHwwFDXJldHVybiBmYWxzZTtkZAIHDw8WAh8MBQ1yZXR1cm4gZmFsc2U7ZGQCCQ8PFgIfDAUNcmV0dXJuIGZhbHNlO2RkAgsPDxYCHwwFDXJldHVybiBmYWxzZTtkZAIOD2QWBGYPDxYCHwsF6gI8cD5QcmV0dWltIGluZm9ybWFyZWEgY2xpZW50aWxvciBub3N0cmkuIERlIGFjZWVhLCBpbmRpZmVyZW50IGRlIG5lbGFtdXJpcmVhIHBlIGNhcmUgbyBhaSwgYXBlbGVhemEgbm9uLXN0b3AgQ2FsbCBDZW50ZXItdWwgbm9zdHJ1LjxiciAvPkxpc3RhIGNvbXBsZXRhIGEgbnVtZXJlbG9yIGRlIENhbGwgQ2VudGVyIGVzdGUgZGlzcG9uaWJpbGEgPGEgaHJlZj0iLi4vLi4vSW5mb3JtYXRpaS1VdGlsZS9DYWxsLUNlbnRlciI+YWljaTwvYT48YnIgLz48YnIgLz5OZSBwdXRldGkgY29udGFjdGEgZ3JhdHVpdCBjb21wbGV0YW5kIDxhIGhyZWY9Ii4uLy4uL0NvbnRhY3Qtcm8vIj5hY2VzdCBmb3JtdWxhcjwvYT4uPGJyIC8+PGJyIC8+PC9wPmRkAgIPDxYCHwsF0BQ8cD5UcmFuemFjdGlpbGUgaW4gRVVSLCBVU0QsIEdCUCBlZmVjdHVhdGUgcGUgb3Blbi5tYXhpdG91cnMuYmUgc3VudCBzZWN1cml6YXRlIGRlIGNhdHJlIHNpc3RlbXVsIGRlIHBsYXRpIEluZ2VuaWNvICg8YSBocmVmPSJodHRwOi8vd3d3LnBheW1lbnQtc2VydmljZXMuaW5nZW5pY28uY29tLyIgdGFyZ2V0PSJfYmxhbmsiPjxzdHJvbmc+d3d3LnBheW1lbnQtc2VydmljZXMuaW5nZW5pY28uY29tPC9zdHJvbmc+PC9hPikuIFRvYXRlIGluZm9ybWF0aWlsZSBzY2hpbWJhdGUgcGVudHJ1IGEgcHJvY2VzYSBwbGF0YSBzdW50IGNyaXB0YXRlIGZvbG9zaW5kIHByb3RvY29sdWwgU1NMLiBBY2VzdGUgZGF0ZSBudSBwb3QgZmkgZGV0ZWN0YXRlLCBpbnRlcmNlcHRhdGUgc2F1IGZvbG9zaXRlIGRlIGNhdHJlIHRlcnRpIHNpIG51IHN1bnQgcGFzdHJhdGUgaW4gc2lzdGVtZWxlIG5vYXN0cmUgaW5mb3JtYXRpY2UuPGJyIC8+SW5nZW5pY28gZXN0ZSB1biBmdXJuaXpvciBkZSBzZXJ2aWNpaSB0ZWhuaWNlIHNpIG51IHNlIG9jdXBhIGRlIGxpdGlnaWlsZSBsZWdhdGUgZGUgY29tZW56aS4gQWNlc3RlYSB0cmVidWllIHNhIGZpZSByZXpvbHZhdGUgZGlyZWN0IGN1IG9wZW4ubWF4aXRvdXJzLmJlIHNpLyBzYXUgYmFuY2EgZHVtbmVhdm9hc3RyYS48YnIgLz5NYWkgbXVsdGUgaW5mb3JtYXRpaSBkZXNwcmUgSW5nZW5pY286Jm5ic3A7PGEgaHJlZj0iaHR0cDovL3d3dy5wYXltZW50LXNlcnZpY2VzLmluZ2VuaWNvLmNvbS8iIHRhcmdldD0iX2JsYW5rIj48c3Ryb25nPnd3dy5wYXltZW50LXNlcnZpY2VzLmluZ2VuaWNvLmNvbTwvc3Ryb25nPjwvYT48L3A+PHA+Jm5ic3A7PC9wPjxwPjxpbWcgc3R5bGU9ImZsb2F0OiBsZWZ0OyBoZWlnaHQ6IDE1cHg7IG1hcmdpbi1yaWdodDogMTBweDsiIHNyYz0iaW1hZ2VzL2xvZ29fcGF5dS5wbmciIGJvcmRlcj0iMCIgYWx0PSIiIC8+VHJhbnphY3RpaWxlIGluIFJPTiBlZmVjdHVhdGUgcGUgb3Blbi5tYXhpdG91cnMuYmUgc3VudCBzZWN1cml6YXRlIGRlIGNhdHJlIHNpc3RlbXVsIGRlIHBsYXRpIFBheVUgKDxhIGhyZWY9Imh0dHA6Ly93d3cucGF5dS5ybyIgdGFyZ2V0PSJfYmxhbmsiPjxzdHJvbmc+d3d3LnBheXUucm88L3N0cm9uZz48L2E+KS4gVG9hdGUgaW5mb3JtYXRpaWxlIHNjaGltYmF0ZSBwZW50cnUgYSBwcm9jZXNhIHBsYXRhIHN1bnQgY3JpcHRhdGUgZm9sb3NpbmQgcHJvdG9jb2x1bCBTU0wuIEFjZXN0ZSBkYXRlIG51IHBvdCBmaSBkZXRlY3RhdGUsIGludGVyY2VwdGF0ZSBzYXUgZm9sb3NpdGUgZGUgY2F0cmUgdGVydGkgc2kgbnUgc3VudCBwYXN0cmF0ZSBpbiBzaXN0ZW1lbGUgbm9hc3RyZSBpbmZvcm1hdGljZS48YnIgLz5QYXlVIGVzdGUgdW4gZnVybml6b3IgZGUgc2VydmljaWkgdGVobmljZSBzaSBudSBzZSBvY3VwYSBkZSBsaXRpZ2lpbGUgbGVnYXRlIGRlIGNvbWVuemkuIEFjZXN0ZWEgdHJlYnVpZSBzYSBmaWUgcmV6b2x2YXRlIGRpcmVjdCBjdSBvcGVuLm1heGl0b3Vycy5iZSBzaS8gc2F1IGJhbmNhIGR1bW5lYXZvYXN0cmEuPGJyIC8+TWFpIG11bHRlIGluZm9ybWF0aWkgZGVzcHJlIFBheVU6Jm5ic3A7PGEgaHJlZj0iaHR0cDovL3d3dy5wYXl1LnJvIiB0YXJnZXQ9Il9ibGFuayI+PHN0cm9uZz53d3cucGF5dS5ybzwvc3Ryb25nPjwvYT4uPC9wPjxwPiZuYnNwOzwvcD48cD48aW1nIHN0eWxlPSJmbG9hdDogbGVmdDsgaGVpZ2h0OiAxNXB4OyBtYXJnaW4tcmlnaHQ6IDEwcHg7IiBzcmM9ImltYWdlcy9sb2dvX3BheXBhbC5wbmciIGJvcmRlcj0iMCIgYWx0PSIiIC8+VHJhbnphY3RpaWxlIGluIEVVUiwgVVNELCBHQlAsIFNFSyBlZmVjdHVhdGUgcGUgb3Blbi5tYXhpdG91cnMuYmUgc3VudCBzZWN1cml6YXRlIGRlIGNhdHJlIHNpc3RlbXVsIGRlIHBsYXRpIFBheVBhbCAoPGEgaHJlZj0iaHR0cHM6Ly93d3cucGF5cGFsLmNvbS91cy93ZWJhcHBzL21wcC9wYXlwYWwtcG9wdXAiPjxzdHJvbmc+aHR0cHM6Ly93d3cucGF5cGFsLmNvbS91cy93ZWJhcHBzL21wcC9wYXlwYWwtcG9wdXA8L3N0cm9uZz48L2E+KSBTZSBhY2NlcHRhIHBsYXRpIHByaW4gVmlzYSBzaSBNYXN0ZXJjYXJkLiBOdSBzZSBhY2NlcHRhIEFtZXJpY2FuIEV4cHJlc3MuIFRvYXRlIGluZm9ybWF0aWlsZSBzY2hpbWJhdGUgcGVudHJ1IGEgcHJvY2VzYSBwbGF0YSBzdW50IGNyaXB0YXRlIGZvbG9zaW5kIHByb3RvY29sdWwgU1NMLiBBY2VzdGUgZGF0ZSBudSBwb3QgZmkgZGV0ZWN0YXRlLCBpbnRlcmNlcHRhdGUgc2F1IGZvbG9zaXRlIGRlIGNhdHJlIHRlcnRpIHNpIG51IHN1bnQgcGFzdHJhdGUgaW4gc2lzdGVtZWxlIG5vYXN0cmUgaW5mb3JtYXRpY2UuPGJyIC8+IFBheVBhbCBlc3RlIHVuIGZ1cm5pem9yIGRlIHNlcnZpY2lpIHRlaG5pY2Ugc2kgbnUgc2Ugb2N1cGEgZGUgbGl0aWdpaWxlIGxlZ2F0ZSBkZSBjb21lbnppLiBBY2VzdGVhIHRyZWJ1aWUgc2EgZmllIHJlem9sdmF0ZSBkaXJlY3QgY3Ugb3Blbi5tYXhpdG91cnMuYmUgc2kvIHNhdSBiYW5jYSBkdW1uZWF2b2FzdHJhLjxiciAvPiBNYWkgbXVsdGUgaW5mb3JtYXRpaSBkZXNwcmUgUGF5UGFsOiZuYnNwOzxhIGhyZWY9Imh0dHBzOi8vd3d3LnBheXBhbC5jb20vIj48c3Ryb25nPmh0dHBzOi8vd3d3LnBheXBhbC5jb20vPC9zdHJvbmc+PC9hPjwvcD5kZAIKD2QWAmYPZBYCZg9kFgICAQ9kFgICAQ8WAh8BAg4WHGYPZBYEZg8VAQZCRUxHSUFkAgEPFgIfAQIBFgJmD2QWAmYPFQIXL0Rlc3RpbmF0aWkvNF9CcnV4ZWxsZXMJQnJ1eGVsbGVzZAIBD2QWBGYPFQEFQ0lQUlVkAgEPFgIfAQIBFgJmD2QWAmYPFQIWL0Rlc3RpbmF0aWkvMTlfTGFybmFjYQdMYXJuYWNhZAICD2QWBGYPFQEJRGFuZW1hcmNhZAIBDxYCHwECARYCZg9kFgJmDxUCGi9EZXN0aW5hdGlpLzExMTZfQ29wZW5oYWdhCUNvcGVuaGFnYWQCAw9kFgRmDxUBBkZSQU5UQWQCAQ8WAh8BAgQWCGYPZBYCZg8VAhkvRGVzdGluYXRpaS8xMTE1X0JvcmRlYXV4CEJvcmRlYXV4ZAIBD2QWAmYPFQITL0Rlc3RpbmF0aWkvMjFfTHlvbgRMeW9uZAICD2QWAmYPFQITL0Rlc3RpbmF0aWkvNDZfTmlzYQROaXNhZAIDD2QWAmYPFQIfL0Rlc3RpbmF0aWkvMTVfUGFyaXNfKEJlYXV2YWlzKRBQYXJpcyAoQmVhdXZhaXMpZAIED2QWBGYPFQEIR0VSTUFOSUFkAgEPFgIfAQIEFghmD2QWAmYPFQIVL0Rlc3RpbmF0aWkvNjJfQmVybGluBkJlcmxpbmQCAQ9kFgJmDxUCFy9EZXN0aW5hdGlpLzEwNF9IYW1idXJnB0hhbWJ1cmdkAgIPZBYCZg8VAhMvRGVzdGluYXRpaS81OF9Lb2xuBEtvbG5kAgMPZBYCZg8VAhgvRGVzdGluYXRpaS8xMF9TdHV0dGdhcnQJU3R1dHRnYXJ0ZAIFD2QWBGYPFQEGR1JFQ0lBZAIBDxYCHwECAhYEZg9kFgJmDxUCFC9EZXN0aW5hdGlpLzkyX0F0ZW5hBUF0ZW5hZAIBD2QWAmYPFQIWL0Rlc3RpbmF0aWkvNzVfU2Fsb25pYwdTYWxvbmljZAIGD2QWBGYPFQEHSXJsYW5kYWQCAQ8WAh8BAgEWAmYPZBYCZg8VAhUvRGVzdGluYXRpaS81OV9EdWJsaW4GRHVibGluZAIHD2QWBGYPFQEGSXNyYWVsZAIBDxYCHwECARYCZg9kFgJmDxUCFy9EZXN0aW5hdGlpLzgzX1RlbF9Bdml2CFRlbCBBdml2ZAIID2QWBGYPFQEGSVRBTElBZAIBDxYCHwECDRYaZg9kFgJmDxUCFy9EZXN0aW5hdGlpLzEwOV9BbGdoZXJvB0FsZ2hlcm9kAgEPZBYCZg8VAhMvRGVzdGluYXRpaS85M19CYXJpBEJhcmlkAgIPZBYCZg8VAhYvRGVzdGluYXRpaS8xNF9Cb2xvZ25hB0JvbG9nbmFkAgMPZBYCZg8VAhYvRGVzdGluYXRpaS8zNV9DYXRhbmlhB0NhdGFuaWFkAgQPZBYCZg8VAhcvRGVzdGluYXRpaS85MV9GbG9yZW50YQhGbG9yZW50YWQCBQ9kFgJmDxUCHC9EZXN0aW5hdGlpLzY2X0xhbWV6aWFfVGVybWUNTGFtZXppYSBUZXJtZWQCBg9kFgJmDxUCFy9EZXN0aW5hdGlpLzExMTNfTWlsYW5vBk1pbGFub2QCBw9kFgJmDxUCHy9EZXN0aW5hdGlpLzg1X01pbGFub18oQmVyZ2FtbykQTWlsYW5vIChCZXJnYW1vKWQCCA9kFgJmDxUCHi9EZXN0aW5hdGlpLzg4X01pbGFub18oTGluYXRlKQ9NaWxhbm8gKExpbmF0ZSlkAgkPZBYCZg8VAhUvRGVzdGluYXRpaS8zNF9OYXBvbGkGTmFwb2xpZAIKD2QWAmYPFQIXL0Rlc3RpbmF0aWkvMTEyX1Blc2NhcmEHUGVzY2FyYWQCCw9kFgJmDxUCEy9EZXN0aW5hdGlpLzE4X1JvbWEEUm9tYWQCDA9kFgJmDxUCFS9EZXN0aW5hdGlpLzg3X1RvcmlubwZUb3Jpbm9kAgkPZBYEZg8VAQ5NQVJFQSBCUklUQU5JRWQCAQ8WAh8BAgQWCGYPZBYCZg8VAhovRGVzdGluYXRpaS8xMDVfQmlybWluZ2hhbQpCaXJtaW5naGFtZAIBD2QWAmYPFQIXL0Rlc3RpbmF0aWkvMTAzX0dsYXNnb3cHR2xhc2dvd2QCAg9kFgJmDxUCGC9EZXN0aW5hdGlpLzg2X0xpdmVycG9vbAlMaXZlcnBvb2xkAgMPZBYCZg8VAh0vRGVzdGluYXRpaS8zMF9Mb25kcmFfKEx1dG9uKQ5Mb25kcmEgKEx1dG9uKWQCCg9kFgRmDxUBClBvcnR1Z2FsaWFkAgEPFgIfAQIBFgJmD2QWAmYPFQIXL0Rlc3RpbmF0aWkvMjBfTGlzYWJvbmEITGlzYWJvbmFkAgsPZBYEZg8VAQdST01BTklBZAIBDxYCHwECChYUZg9kFgJmDxUCEy9EZXN0aW5hdGlpLzZfQmFjYXUFQmFjYXVkAgEPZBYCZg8VAiAvRGVzdGluYXRpaS83M19CcmFzb3ZfLV90cmFuc2ZlchFCcmFzb3YgLSB0cmFuc2ZlcmQCAg9kFgJmDxUCGC9EZXN0aW5hdGlpLzcwX0J1Y3VyZXN0aQlCdWN1cmVzdGlkAgMPZBYCZg8VAhovRGVzdGluYXRpaS84NF9DbHVqLU5hcG9jYQtDbHVqLU5hcG9jYWQCBA9kFgJmDxUCGC9EZXN0aW5hdGlpLzkwX0NvbnN0YW50YQlDb25zdGFudGFkAgUPZBYCZg8VAiMvRGVzdGluYXRpaS81Nl9Db25zdGFudGFfLV90cmFuc2ZlchRDb25zdGFudGEgLSB0cmFuc2ZlcmQCBg9kFgJmDxUCEy9EZXN0aW5hdGlpLzgyX0lhc2kESWFzaWQCBw9kFgJmDxUCFy9EZXN0aW5hdGlpLzExMTRfT3JhZGVhBk9yYWRlYWQCCA9kFgJmDxUCFC9EZXN0aW5hdGlpLzI4X1NpYml1BVNpYml1ZAIJD2QWAmYPFQIYL0Rlc3RpbmF0aWkvNjFfVGltaXNvYXJhCVRpbWlzb2FyYWQCDA9kFgRmDxUBBlNQQU5JQWQCAQ8WAh8BAgkWEmYPZBYCZg8VAhkvRGVzdGluYXRpaS8xMTE3X0FsaWNhbnRlCEFsaWNhbnRlZAIBD2QWAmYPFQIXL0Rlc3RpbmF0aWkvOV9CYXJjZWxvbmEJQmFyY2Vsb25hZAICD2QWAmYPFQIZL0Rlc3RpbmF0aWkvMTAyX0Nhc3RlbGxvbglDYXN0ZWxsb25kAgMPZBYCZg8VAhQvRGVzdGluYXRpaS83NF9JYml6YQVJYml6YWQCBA9kFgJmDxUCFS9EZXN0aW5hdGlpLzIyX01hZHJpZAZNYWRyaWRkAgUPZBYCZg8VAhQvRGVzdGluYXRpaS83X01hbGFnYQZNYWxhZ2FkAgYPZBYCZg8VAiEvRGVzdGluYXRpaS8xMDdfUGFsbWFfZGVfTWFsbG9yY2ERUGFsbWEgZGUgTWFsbG9yY2FkAgcPZBYCZg8VAhgvRGVzdGluYXRpaS8xMTE4X1NldmlsbGEHU2V2aWxsYWQCCA9kFgJmDxUCFy9EZXN0aW5hdGlpLzExX1ZhbGVuY2lhCFZhbGVuY2lhZAIND2QWBGYPFQEGU3VlZGlhZAIBDxYCHwECARYCZg9kFgJmDxUCGS9EZXN0aW5hdGlpLzEwNl9TdG9ja2hvbG0JU3RvY2tob2xtZAILDw8WAh8ABTBodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0NvbmRpdGlpLURlLUNhbGF0b3JpZS9kZAIMDw8WAh8ABS5odHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL1Rlcm1lbmktU2ktQ29uZGl0aWkvZGQCDQ8PFgIfAAUiaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9DYXJpZXJlL2RkAg4PDxYCHwAFPmh0dHA6Ly93d3cuYmx1ZWFpcndlYi5jb20vSW5mb3JtYXRpaS1VdGlsZS9Db211bmljYXRlLURlLVByZXNhZGQCDw8PFgIfAAU1aHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9JbmZvcm1hdGlpLVV0aWxlL05ld3NsZXR0ZXJkZAIQDw8WAh8ABSVodHRwOi8vd3d3LmJsdWVhaXJ3ZWIuY29tL0NvbnRhY3Qtcm8vZGQCEQ8PFgIfAAUhaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9PcGluaWkvZGQCEg8PFgIfAAUlaHR0cDovL3d3dy5ibHVlYWlyd2ViLmNvbS9IYXJ0YS1TaXRlL2RkAhUPZBYCZg9kFgICAQ9kFgYCAQ8PFgIfCGhkZAIDDw8WBh8JBQVyZWQxMR8LBXxEYXRvcml0YSBwZXJpb2FkZWkgZGUgaW5hY3Rpdml0YXRlIHNlc2l1bmVhIGR1bW5lYXZvYXN0cmEgYSBleHBpcmF0Ljxici8+QXBhc2F0aSBPSyBwZW50cnUgYSByZWluY2VwZSBwcm9jZXN1bCBkZSByZXplcnZhcmUuHwoCAmRkAgUPDxYCHwwFHGxvY2F0aW9uLmhyZWY9J2RlZmF1bHQuYXNweCdkZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WBAUyY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRCb29raW5nQm94MSRyZG9Sb3VuZFRyaXAFL2N0bDAwJENvbnRlbnRQbGFjZUhvbGRlcjEkQm9va2luZ0JveDEkcmRvT25lV2F5BS9jdGwwMCRDb250ZW50UGxhY2VIb2xkZXIxJEJvb2tpbmdCb3gxJHJkb09uZVdheQUmY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRja2JBZ3JlZW1lbnQCCu9TUVJ8Q1YmBlv7eVaxIwBRPKSQHzwrbxIuuAE8Pw==",
            $inputs['__VIEWSTATE']);

    }

    public function testSearchResponse()
    {
        $Scraper = new Scraper();
        $formreq =   $Scraper->getRedirectForm(self::readFile('response_success.html'));

        $this->assertEquals('/SelectLowFare.aspx', $formreq->action);
        $this->assertEmpty($formreq->post);
    }

    public function testParseFlightsInfo()
    {
        $Scraper = new Scraper();
        $ctr =   $Scraper->parseFlightSearchResults(self::readFile('flight_results.html'));

        $this->assertCount(5, $ctr->outbound);
        $this->assertCount(5, $ctr->inbound);

        $this->assertNotEmpty($ctr->outbound['2017-02-04']);
        $this->assertNotEmpty($ctr->inbound['2017-02-09']);

        $this->assertEquals(74.94, $ctr->outbound['2017-02-04'][0]->price_allpassengers->amount);
        $this->assertEquals('EUR', $ctr->outbound['2017-02-04'][0]->price_allpassengers->currency);

        // assert internal pointer position of segments.. important for determining departure/arrival flights
        $this->assertSame('4020', current($ctr->outbound['2017-02-04'][0]->segments)->number);
        $this->assertSame('4008', current($ctr->inbound['2017-02-09'][0]->segments)->number);
        // end

        // make sure json dates are not datetime objects
        $json   =   \json_encode($ctr);

        $this->assertNotContains('timezone', $json);
        $this->assertNotContains('timezone_type', $json);
        // end json assert
    }

    public function testDecodeFlightSegments()
    {
        $journey    =   '0B~4020~ ~~TXL~02/04/2017 10:10~TRN~02/04/2017 11:50~~^0B~4107~ ~~TRN~02/04/2017 16:30~FCO~02/04/2017 17:45~~';
        $decoded    =   FlightsContainer::decodeFlightSegments($journey);
        $as_array   =   iterator_to_array($decoded);

        $this->assertCount(2, $as_array);
        $this->assertEquals('0B', $as_array[0]->operator);
        $this->assertEquals('4020', $as_array[0]->number);
        $this->assertEquals('0B', $as_array[0]->operator);
        $this->assertEquals('4107', $as_array[1]->number);
        $this->assertEquals('TXL', $as_array[0]->origin);
            $this->assertEquals('TRN', $as_array[1]->origin);
            $this->assertEquals('TRN', $as_array[0]->destination);
        $this->assertEquals('FCO', $as_array[1]->destination);
        $this->assertEquals('201702041010', $as_array[0]->departure->format('YmdHi'));
        $this->assertEquals('201702041150', $as_array[0]->arrival->format('YmdHi'));
        $this->assertEquals('201702041630', $as_array[1]->departure->format('YmdHi'));
        $this->assertEquals('201702041745', $as_array[1]->arrival->format('YmdHi'));
    }

    public function testParsePrice()
    {
        $price  =   '79,96 EUR';
        $obj    =   FlightsContainer::parsePriceString($price);

        $this->assertInternalType('object', $obj);
        $this->assertObjectHasAttribute('amount', $obj);
        $this->assertObjectHasAttribute('currency', $obj);
        $this->assertEquals('EUR', $obj->currency);
        $this->assertEquals(79.96, $obj->amount);
    }

    public function testParsePriceDecimal()
    {
        $price  =   '79.96 usd';
        $obj    =   FlightsContainer::parsePriceString($price);

        $this->assertInternalType('object', $obj);
        $this->assertObjectHasAttribute('amount', $obj);
        $this->assertObjectHasAttribute('currency', $obj);
        $this->assertEquals('USD', $obj->currency);
        $this->assertEquals(79.96, $obj->amount);
    }


}
