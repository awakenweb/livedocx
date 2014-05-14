<?php

/*
 * The MIT License
 *
 * Copyright 2014 Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>.
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

namespace Awakenweb\Livedocx;

use Awakenweb\Livedocx\Tools\Result;
use Awakenweb\Livedocx\Tools\ResultSet;

class Tools
{

    public function pingService($host, $port = 443, $attempts = 5, $threshold = 1000, $timeout = 3000)
    {
        $resultset = new ResultSet;
        for ($i = 1; $i <= $attempts; $i++) {
            $resultset->addResult($this->getResult($i, $host, $port, $threshold, $timeout));
        }
        return $resultset;
    }

    /**
     *
     * @param int $position
     * @param string $host
     * @param int $port
     * @param int $threshold
     * @param int $timeout
     *
     * @return Result
     */
    protected function getResult($position, $host, $port, $threshold, $timeout)
    {
        $time = $this->ping($host, $port, $timeout);
        if ($time < 0) {
            $status = 'lost';
            $time   = $timeout;
        }
        if ($time > 0 && $time < $threshold) {
            $status = 'ok';
        }
        if ($time >= $threshold) {
            $status = 'slow';
        }
        return new Result($position, $status, $time);
    }

    /**
     *
     * @param string $host
     * @param int $port
     * @param int $timeout
     *
     * @return int
     */
    protected function ping($host, $port, $timeout)
    {
        $tB = microtime(true);
        $fP = fSockOpen($host, $port, $errno, $errstr, $timeout);
        if (!$fP) {
            return -1;
        }
        $tA = microtime(true);
        return round((($tA - $tB) * 1000), 0);
    }

}
