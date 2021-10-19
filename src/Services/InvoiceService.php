<?php

namespace Pharaoh\Invoice\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Pharaoh\Invoice\Exceptions\InvoiceException;

class InvoiceService
{
    /**
     * 開立發票工具的設定參數
     *
     * @var array
     */
    private array $settings = [];

    /**
     * 請求參數
     *
     * @var array
     */
    private array $requestData = [];

    public function __construct()
    {
        $this->settings = config('invoice');

        $this->requestData = [
            'MerchantID' => Arr::get($this->settings, 'merchant_id'),
            'RqHeader' => [
                'Timestamp' => now()->timestamp,
                'Revision' => Arr::get($this->settings, 'vision')
            ],
            'Data' => [
                'MerchantID' => Arr::get($this->settings, 'merchant_id'),
            ],
        ];
    }

    /**
     * 開立一般發票
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function issue(array $data): array
    {
        try {
            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('Issue');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new InvoiceException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 開立延遲發票
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function delayIssue(array $data): array
    {
        try {
            $this->requestData['Data']['PayType'] = '2';
            $this->requestData['Data']['PayAct'] = 'ECPAY';

            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('delayIssue');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new InvoiceException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 觸發開立發票
     *
     * @param string $transactionNumber
     * @return array
     * @throws InvoiceException
     */
    public function triggerIssue(string $transactionNumber): array
    {
        try {
            $this->requestData['Data']['PayType'] = '2';
            $this->requestData['Data']['Tsr'] = $transactionNumber;

            $this->requestData['Data'] = $this->encryptData($this->requestData['Data']);

            $responseData = $this->httpRequest('triggerIssue');

            $rtnCode = Arr::get($responseData, 'RtnCode');

            if (!in_array($rtnCode, ['4000003', '4000004'])) {
                throw new InvoiceException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 取消延遲開立發票
     *
     * @param string $transactionNumber
     * @return array
     * @throws InvoiceException
     */
    public function cancelDelayIssue(string $transactionNumber): array
    {
        try {
            $this->requestData['Data']['Tsr'] = $transactionNumber;

            $this->requestData['Data'] = $this->encryptData($this->requestData['Data']);

            $responseData = $this->httpRequest('cancelDelayIssue');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new InvoiceException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * HTTP請求
     *
     * @param string $method
     * @return array
     */
    private function httpRequest(string $method): array
    {
        $url = Arr::get($this->settings, 'invoice_url') . $method;
        $responseRawData = Http::post($url, $this->requestData)->json();
        return $this->decryptData($responseRawData['Data']);
    }

    /**
     * 加密請求參數
     *
     * @param array $data
     * @return string
     */
    private function encryptData(array $data): string
    {
        // URLEncode 編碼
        $urlEncodeString = urlencode(json_encode($data));

        // AES 加密
        return base64_encode(
            openssl_encrypt($urlEncodeString, 'aes-128-cbc', $this->settings['hash_key'], 1, $this->settings['hash_iv'])
        );
    }

    private function decryptData(string $encryptString): array
    {
        // AES 解密
        $urlEncodeString = openssl_decrypt($encryptString, 'aes-128-cbc', $this->settings['hash_key'], 0, $this->settings['hash_iv']);

        // URLDecode 解碼
        return json_decode(urldecode($urlEncodeString), true);
    }
}
