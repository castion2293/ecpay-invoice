<?php

namespace Pharaoh\Invoice;

use Pharaoh\Invoice\Exceptions\InvoiceException;
use Pharaoh\Invoice\Services\InvoiceService;

class Invoice
{
    private InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * 開立一般發票
     *
     * @param array $data
     * @return array
     * @throws Exceptions\InvoiceException
     */
    public function issue(array $data): array
    {
        $dataRequiredFields = ['RelateNumber', 'Print', 'Donation', 'TaxType', 'SalesAmount', 'InvType'];
        $itemsRequiredFields = ['ItemName', 'ItemCount', 'ItemWord', 'ItemPrice', 'ItemAmount'];

        $this->checkRequiredFields($dataRequiredFields, $data);
        if (isset($data['Items'])) {
            foreach ($data['Items'] as $item) {
                $this->checkRequiredFields($itemsRequiredFields, $item);
            }
        }

        return $this->invoiceService->issue($data);
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
        $dataRequiredFields = ['RelateNumber', 'Print', 'Donation', 'TaxType', 'SalesAmount', 'InvType', 'DelayFlag', 'DelayDay', 'Tsr'];
        $itemsRequiredFields = ['ItemName', 'ItemCount', 'ItemWord', 'ItemPrice', 'ItemAmount'];

        $this->checkRequiredFields($dataRequiredFields, $data);
        if (isset($data['Items'])) {
            foreach ($data['Items'] as $item) {
                $this->checkRequiredFields($itemsRequiredFields, $item);
            }
        }

        return $this->invoiceService->delayIssue($data);
    }

    /**
     * 觸發開立發票
     *
     * @param string $transactionNumber
     * @return array
     */
    public function triggerIssue(string $transactionNumber): array
    {
    }

    /**
     * 取消延遲開立發票
     *
     * @param string $transactionNumber
     * @return array
     */
    public function cancelDelayIssue(string $transactionNumber): array
    {
    }

    /**
     * 開立一般折讓發票
     *
     * @param array $data
     * @return array
     */
    public function allowance(array $data): array
    {
    }

    /**
     * 開立線上折讓發票
     *
     * @param array $data
     * @return array
     */
    public function allowanceByCollegiate(array $data): array
    {
    }

    /**
     * 作廢發票
     *
     * @param array $data
     * @return array
     */
    public function invalide(array $data): array
    {
    }

    /**
     * 作廢折讓發票
     *
     * @param array $data
     * @return array
     */
    public function allowanceInvalid(array $data): array
    {
    }

    /**
     * 註銷重開
     *
     * @param array $data
     * @return array
     */
    public function voidWithRelssue(array $data): array
    {
    }

    /**
     * 查詢發票 根據特店自訂編號
     *
     * @param string $relateNumber
     * @return array
     */
    public function getIssueByRelateNumber(string $relateNumber): array
    {
    }

    /**
     * 查詢發票 根據發票號及開立日期
     *
     * @param string $invoiceNo
     * @param string $date
     * @return array
     */
    public function getIssueByInvoiceNoAndData(string $invoiceNo, string $date): array
    {
    }

    /**
     * 查詢折攘明細
     *
     * @param string $invoiceNo
     * @param string $allowanceNo
     * @return array
     */
    public function getAllowance(string $invoiceNo, string $allowanceNo): array
    {
    }

    /**
     * 查詢作廢發票明細
     *
     * @param string $relateNumber
     * @param string $invoiceNo
     * @param string $invoiceDate
     * @return array
     */
    public function getInvalid(string $relateNumber, string $invoiceNo, string $invoiceDate): array
    {
    }

    /**
     * 查詢作廢折讓明細
     *
     * @param string $invoiceNo
     * @param string $allowanceNo
     * @return array
     */
    public function getAllowanceInvalid(string $invoiceNo, string $allowanceNo): array
    {
    }

    /**
     * 查詢自軌
     *
     * @param array $data
     * @return array
     */
    public function getInvoiceWordSetting(array $data): array
    {
    }

    /**
     * 發送發票通知
     *
     * @param array $data
     * @return array
     */
    public function invoiceNotify(array $data): array
    {
    }

    /**
     * 手機條碼驗證
     *
     * @param string $barcode
     * @return array
     */
    public function checkBarcode(string $barcode): array
    {
    }

    /**
     * 捐贈碼驗證
     *
     * @param string $loveCode
     * @return array
     */
    public function checkLoveCode(string $loveCode): array
    {
    }

    /**
     * 檢查必填欄位
     *
     * @param array $requiredFields
     * @param array $data
     * @throws InvoiceException
     */
    private function checkRequiredFields(array $requiredFields, array $data)
    {
        $requiredFields = array_diff($requiredFields, array_keys($data));
        if (!empty($requiredFields)) {
            throw new InvoiceException('必填欄位: ' . implode(',', $requiredFields) . ' 未填入');
        }
    }
}
