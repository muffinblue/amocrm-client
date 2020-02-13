<?php /** @noinspection PhpVariableNamingConventionInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @noinspection ParameterDefaultValueIsNotNullInspection */

namespace AmoCrm;

use AmoCrm\Exception\EnvException;
use AmoCrm\Exception\RequestException;

class CurlClient
{
    public function request($method, $url, $params = [], $data = [], $headers = []) {

        $options = $this->options($method, $url, $params, $data, $headers);

        /** @noinspection BadExceptionsProcessingInspection */
        try {

            if (!$curl = curl_init()) {
                throw new EnvException('Unable to initialize cURL');
            }

            if (!curl_setopt_array($curl, $options)) {
                throw new EnvException(curl_error($curl));
            }

            if (!$response = curl_exec($curl)) {
                throw new EnvException(curl_error($curl));
            }

            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($status < 200 && $status > 204) {
                throw new RequestException($status);
            }

            return json_decode($response, true);

        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ErrorException $e) {

            if (isset($curl) && is_resource($curl)) {
                curl_close($curl);
            }

            throw $e;
        }
    }

    public function options($method, $url, $params = [], $data = [], $headers = []) {
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        foreach ($headers as $key => $value) {
            $options[CURLOPT_HTTPHEADER][] = "$key: $value";
        }

        $body = $this->buildQuery($params);
        if ($body) {
            $options[CURLOPT_URL] .= '?' . $body;
        }

        switch (strtoupper(trim($method))) {
            case 'GET':
                $options[CURLOPT_HTTPGET] = true;
                break;
            case 'POST':
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
                break;
            default:
                $options[CURLOPT_CUSTOMREQUEST] = strtoupper(trim($method));
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
                break;
        }

        return $options;
    }

    public function buildQuery($params) {
        $parts = [];

        if (is_string($params)) {
            return $params;
        }

        $params = $params ?: [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $parts[] = urlencode((string)$key) . '=' . urlencode((string)$item);
                }
            } else {
                $parts[] = urlencode((string)$key) . '=' . urlencode((string)$value);
            }
        }

        return implode('&', $parts);
    }
}