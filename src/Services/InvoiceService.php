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

            $responseData = $this->httpRequest('issue');

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
     * 開立一般折讓發票
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function allowance(array $data): array
    {
        try {
            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('allowance');

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
     * 開立線上折讓發票(通知開立)
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function allowanceByCollegiate(array $data): array
    {
        try {
            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('allowanceByCollegiate');

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
     * 作廢發票
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function invalid(array $data): array
    {
        try {
            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('invalid');

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
     * 作廢折讓發票
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function allowanceInvalid(array $data): array
    {
        try {
            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('allowanceInvalid');

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
     * 註銷重開
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function voidWithReIssue(array $data): array
    {
        try {
            $requestData = [
                'VoidModel' => Arr::only($data, ['InvoiceNo', 'VoidReason']),
                'IssueModel' => array_merge(Arr::except($data, ['InvoiceNo', 'VoidReason']), $this->requestData['Data'])
            ];

            $this->requestData['Data'] = $this->encryptData($requestData);

            $responseData = $this->httpRequest('voidWithReIssue');

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
     * 查詢發票 根據特店自訂編號
     *
     * @param string $relateNumber
     * @return array
     * @throws InvoiceException
     */
    public function getIssueByRelateNumber(string $relateNumber): array
    {
        try {
            $this->requestData['Data']['RelateNumber'] = $relateNumber;

            $this->requestData['Data'] = $this->encryptData($this->requestData['Data']);

            $responseData = $this->httpRequest('getIssue');

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
     * 查詢發票 根據發票號及開立日期
     *
     * @param string $invoiceNo
     * @param string $date
     * @return array
     * @throws InvoiceException
     */
    public function getIssueByInvoiceNoAndData(string $invoiceNo, string $date): array
    {
        try {
            $this->requestData['Data']['InvoiceNo'] = $invoiceNo;
            $this->requestData['Data']['InvoiceDate'] = $date;

            $this->requestData['Data'] = $this->encryptData($this->requestData['Data']);

            $responseData = $this->httpRequest('getIssue');

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
     * 查詢折攘明細
     *
     * @param string $invoiceNo
     * @param string $allowanceNo
     * @return array
     * @throws InvoiceException
     */
    public function getAllowance(string $invoiceNo, string $allowanceNo): array
    {
        try {
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 查詢作廢發票明細
     *
     * @param string $relateNumber
     * @param string $invoiceNo
     * @param string $invoiceDate
     * @return array
     * @throws InvoiceException
     */
    public function getInvalid(string $relateNumber, string $invoiceNo, string $invoiceDate): array
    {
        try {
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 查詢作廢折讓明細
     *
     * @param string $invoiceNo
     * @param string $allowanceNo
     * @return array
     * @throws InvoiceException
     */
    public function getAllowanceInvalid(string $invoiceNo, string $allowanceNo): array
    {
        try {
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 查詢自軌
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function getInvoiceWordSetting(array $data): array
    {
        try {
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 發送發票通知
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function invoiceNotify(array $data): array
    {
        try {
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 手機條碼驗證
     *
     * @param string $barcode
     * @return array
     * @throws InvoiceException
     */
    public function checkBarcode(string $barcode): array
    {
        try {
        } catch (\Exception $exception) {
            throw new InvoiceException($exception->getMessage());
        }
    }

    /**
     * 捐贈碼驗證
     *
     * @param string $loveCode
     * @return array
     * @throws InvoiceException
     */
    public function checkLoveCode(string $loveCode): array
    {
        try {
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
