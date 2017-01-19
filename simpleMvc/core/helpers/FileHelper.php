<?php
/*
 * This file is part of the SimpleMvc package.

 * @copyright 2016 Jamie Kim
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleMvc;

Class FileHelper
{
    /*
    * This file is part of the SimpleMvc package.

    * @$filename : filename to show user.
    * $serverFileName : filename in server.
    * $expires : caching expires in second.
    * $speedLimit : download speed in KB.
      
    * @return boolean
    */   
    public static function download($filename, $serverFileName, $expires = 0, $speedLimit = 0)
    {
        // check server filename
        if (!file_exists($serverFileName) || !is_readable($serverFileName)) {
            return false;
        }
        if (($filesize = filesize($serverFileName)) == 0) {
            return false;
        }
        if (($fp = @fopen($serverFileName, 'rb')) === false) {
            return false;
        }

        //convert to safe characters.
        $illegal = ['\\', '/', '<', '>', '{', '}', ':', ';', '|', '"', '~', '`', '@', '#', '$', '%', '^', '&', '*', '?'];
        $replace = ['', '', '(', ')', '(', ')', '_', ',', '_', '', '_', '\'', '_', '_', '_', '_', '_', '_', '', ''];
        
        $filename = str_replace($illegal, $replace, $filename);
        $filename = preg_replace('/([\\x00-\\x1f\\x7f\\xff]+)/', '', $filename);
        $filename = trim(preg_replace('/[\\pZ\\pC]+/u', ' ', $filename));
        $filename = trim($filename, ' .-_');
        $filename = preg_replace('/__+/', '_', $filename);
        if ($filename === '') {
            return false;
        }

        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $old_ie = (bool)preg_match('#MSIE [3-8]\.#', $ua);

        //check browser exceptions
        if (preg_match('/^[a-zA-Z0-9_.-]+$/', $filename)) {
            $header = 'filename="' . $filename . '"';
        }
        elseif ($old_ie || preg_match('#Firefox/(\d+)\.#', $ua, $matches) && $matches[1] < 5) {
            $header = 'filename="' . rawurlencode($filename) . '"';
        }
        elseif (preg_match('#Chrome/(\d+)\.#', $ua, $matches) && $matches[1] < 11) {
            $header = 'filename=' . $filename;
        }
        elseif (preg_match('#Safari/(\d+)\.#', $ua, $matches) && $matches[1] < 6) {
            $header = 'filename=' . $filename;
        }
        elseif (preg_match('#Android #', $ua, $matches)) {
            $header = 'filename="' . $filename . '"';
        }
        else {
            $header = "filename*=UTF-8''" . rawurlencode($filename) . '; filename="' . rawurlencode($filename) . '"';
        }

        //caching.
        if (!$expires) {

            if ($old_ie) {
                header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
                header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
            }
            else {
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
            }
        }
        else {
            header('Cache-Control: max-age=' . (int)$expires);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (int)$expires) . ' GMT');
        }

        if (isset($_SERVER['HTTP_RANGE']) && preg_match('/^bytes=(\d+)-/', $_SERVER['HTTP_RANGE'], $matches)) {
            $rangeStart = $matches[1];
            if ($rangeStart < 0 || $rangeStart > $filesize) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                return false;
            }
            header('HTTP/1.1 206 Partial Content');
            header('Content-Range: bytes ' . $rangeStart . '-' . ($filesize - 1) . '/' . $filesize);
            header('Content-Length: ' . ($filesize - $rangeStart));
        } else {
            $range_start = 0;
            header('Content-Length: ' . $filesize);
        }

        header('Accept-Ranges: bytes');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; ' . $header);

        while (ob_get_level()) {
            ob_end_clean();
        }

        $blockSize = 16 * 1024;
        $speedSleep = $speedLimit > 0 ? round(($blockSize / $speedLimit / 1024) * 1000000) : 0;

        $buffer = '';
        if ($rangeStart > 0) {
            fseek($fp, $rangeStart);
            $alignment = (ceil($rangeStart / $blockSize) * $blockSize) - $rangeStart;
            if ($alignment > 0) {
                $buffer = fread($fp, $alignment);
                echo $buffer; unset($buffer); flush();
            }
        }
        while (!feof($fp)) {
            $buffer = fread($fp, $blockSize);
            echo $buffer; unset($buffer); flush();
            usleep($speedSleep);
        }

        fclose($fp);

        return true;
    }
}