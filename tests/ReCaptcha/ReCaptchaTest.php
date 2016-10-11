<?php
/**
 * This is a PHP library that handles calling reCAPTCHA.
 *
 * @copyright Copyright (c) 2015, Google Inc.
 * @link      http://www.google.com/recaptcha
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace ReCaptcha;

class ReCaptchaTest extends \PHPUnit_Framework_TestCase
{
    // SecretKey from https://developers.google.com/recaptcha/docs/faq#id-like-to-run-automated-tests-with-recaptcha-v2-what-should-i-do
    const TEST_SECRET_KEY = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
    const TEST_G_RESPONSE = '03AHJ_VustjIfiLhx5siwc0V43Et0bm2G7YOjZPYQv-VZnav20gLtrVNhMRkql9HoWaGtRTPKqHIfuRTf1BhWdQVoiSdwYVQCOXFg6Db96xzh9U2rRGSBSKs9hPXPAW_5YxTbFlN2tNJ5fJySSqNhOhNY9qjpZdcc7rdCP53zYbumW5Asaq9Oke2o1MP7LeVrOAtjZxdSjLF8oXIK0QlPGoKX1e6vU-oAfbuyql0n_NEa7AeDa8GbRXA8reNAhzbcV2_Q_Lw52tXyX7BTr3XIJTv0BbhXbc3PcL4Sc6b0hvXWkOU7J0c3bxzLPXgdi9jVt9E6ML_XKsg-s9oHdMhTU2eOhMnhKZwXi2Tuc44FTX_gcr_YR1m3brpO8vv4183EhnI_1pHhFKEO_CkRNMf4A1OW3zhCDBE7VFN1KvMfdcojg5LsvS01GxbsTzQ4cASmjQJ1YM6TA-Eeh6mwg1qZ5TZBG1OUsx2Sci1yih9OGSewI9keOsQcf3-PNWrRYhGAnX8IM69DyMXcRXPsdd-j8SkNcpdjPhAiph_lZZZCRUR_N1oRWbzg8YARqsIwcFxEwGne1mzxXfsc1WvOM0ehz96oHd72fgnIwQZ3CHpmLjY24610OItJNwbzOExqx9nL-CRNTPbcLDywQ20Zuy4Isw_Fmv22QPBckX-kFvItYTwxMbGpmNmox7uXzgujUqOp7yXHTZDnWkMQ1pUgH2s0HR3Zyxwq_uYpVlzMxFafuUBi1w3-mzKrZmQrNreNKpZjNuGVnit8c7WUpIT2PUEN2giXLWeG8sahrSbVLR5u3HeQ4ZxkJU6q4pzdcFJNgdh-Sz8C8osgkDUgMKCzuKmfqE_6voOWRCDmOmc-q1T8a42BXfCQBwUhLwf0QUpH6iIwmMSkH6a9PvPCTlQFB_iqxxTZxCuxjLS346A';

    /**
     * @expectedException \RuntimeException
     * @dataProvider invalidSecretProvider
     */
    public function testExceptionThrownOnInvalidSecret($invalid)
    {
        $rc = new ReCaptcha($invalid);
    }

    public function invalidSecretProvider()
    {
        return array(
            array(''),
            array(null),
            array(0),
            array(new \stdClass()),
            array(array()),
        );
    }

    public function testVerifyReturnsErrorOnMissingResponse()
    {
        $rc = new ReCaptcha('secret');
        $response = $rc->verify('');
        $this->assertFalse($response->isSuccess());
        $this->assertEquals(array('missing-input-response'), $response->getErrorCodes());
    }

    public function testMockVerifyReturnsResponse()
    {
        $method = $this->getMock('\\ReCaptcha\\RequestMethod', array('submit'));
        $method->expects($this->once())
                ->method('submit')
                ->with($this->callback(function ($params) {

                            return true;
                        }))
                ->will($this->returnValue('{"success": true, "challenge_ts":"2016-10-10T18:42:48Z", "hostname":"www.domain.com"}'));
        ;
        $rc = new ReCaptcha('secret', $method);
        $response = $rc->verify('response');
        $this->assertTrue($response->isSuccess());
    }

    /**
     * @dataProvider integrationDataSet
     */
    public function testVerifyReturnsResponse($response, $success, $errorCodes = [])
    {
        $rc = new ReCaptcha(self::TEST_SECRET_KEY);
        $response = $rc->verify($response);
        $this->assertEquals($success, $response->isSuccess());
        $this->assertEquals($errorCodes, $response->getErrorCodes());
        if ($response->isSuccess()) {
            $this->assertEquals('localhost', $response->getHostName());
        }
    }
    
    public function integrationDataSet() {
        return [
            ['', false, ['missing-input-response']], 
            [uniqid(), false, ['invalid-input-response']],
            [self::TEST_G_RESPONSE, true]
        ];
    }
}
