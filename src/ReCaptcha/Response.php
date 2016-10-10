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

/**
 * The response returned from the service.
 */
class Response
{
    /**
     * Success or failure.
     * @var boolean
     */
    private $success = false;

    /**
     * Optional: Error code strings.
     * @var array
     */
    private $errorCodes = array();

    /**
     * timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
     * @var string
     */
    private $challengeTs;

    /**
     * the hostname of the site where the reCAPTCHA was solved
     * @var string
     */
    private $hostName;

    /**
     * Build the response from the expected JSON returned by the service.
     *
     * @param string $json
     * @return \ReCaptcha\Response
     */
    public static function fromJson($json)
    {
        $responseData = json_decode($json, true);

        if (!$responseData) {
            return (new Response())
                ->setErrorCodes(['invalid-json']);
        }

        $response = (new Response())
            ->setSuccess($responseData['success'])
            ->setErrorCodes(isset($responseData['error-codes']) ? $responseData['error-codes'] : []);
        
        if ($response->isSuccess()) {
            $response
                ->setChallengeTs($responseData['challenge_ts'])
                ->setHostName($responseData['hostname']);
        }
        
        return $response;
    }

    /**
     * Is success?
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Get error codes.
     *
     * @return array
     */
    public function getErrorCodes()
    {
        return $this->errorCodes;
    }

    /**
     * @return string
     */
    public function getChallengeTs()
    {
        return $this->challengeTs;
    }

    /**
     * @param string $challengeTs
     */
    public function setChallengeTs($challengeTs)
    {
        $this->challengeTs = $challengeTs;
        return $this;
    }

    /**
     * @return string
     */
    public function getHostName()
    {
        return $this->hostName;
    }

    /**
     * @param string $hostName
     */
    public function setHostName($hostName)
    {
        $this->hostName = $hostName;
        return $this;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @param array $errorCodes
     */
    public function setErrorCodes($errorCodes)
    {
        $this->errorCodes = $errorCodes;
        return $this;
    }
}
