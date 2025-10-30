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
     * @throws InvoiceException
     */
    public function triggerIssue(string $transactionNumber): array
    {
        return $this->invoiceService->triggerIssue($transactionNumber);
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
        return $this->invoiceService->cancelDelayIssue($transactionNumber);
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
        $dataRequiredFields = ['InvoiceNo', 'InvoiceDate', 'AllowanceNotify', 'AllowanceAmount'];
        $itemsRequiredFields = ['ItemName', 'ItemCount', 'ItemWord', 'ItemPrice', 'ItemAmount'];

        $this->checkRequiredFields($dataRequiredFields, $data);
        if (isset($data['Items'])) {
            foreach ($data['Items'] as $item) {
                $this->checkRequiredFields($itemsRequiredFields, $item);
            }
        }

        return $this->invoiceService->allowance($data);
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
        $dataRequiredFields = ['InvoiceNo', 'InvoiceDate', 'AllowanceNotify', 'AllowanceAmount'];
        $itemsRequiredFields = ['ItemName', 'ItemCount', 'ItemWord', 'ItemPrice', 'ItemAmount'];

        $this->checkRequiredFields($dataRequiredFields, $data);
        if (isset($data['Items'])) {
            foreach ($data['Items'] as $item) {
                $this->checkRequiredFields($itemsRequiredFields, $item);
            }
        }

        return $this->invoiceService->allowanceByCollegiate($data);
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
        $dataRequiredFields = ['InvoiceNo', 'InvoiceDate', 'Reason'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->invoiceService->invalid($data);
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
        $dataRequiredFields = ['InvoiceNo', 'AllowanceNo', 'Reason'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->invoiceService->allowanceInvalid($data);
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
        $dataRequiredFields = ['InvoiceNo', 'VoidReason', 'RelateNumber', 'InvoiceDate', 'Print', 'Donation', 'TaxType', 'SalesAmount', 'InvType'];
        $itemsRequiredFields = ['ItemName', 'ItemCount', 'ItemWord', 'ItemPrice', 'ItemAmount'];

        $this->checkRequiredFields($dataRequiredFields, $data);
        if (isset($data['Items'])) {
            foreach ($data['Items'] as $item) {
                $this->checkRequiredFields($itemsRequiredFields, $item);
            }
        }

        return $this->invoiceService->voidWithReIssue($data);
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
        return $this->invoiceService->getIssueByRelateNumber($relateNumber);
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
        return $this->invoiceService->getIssueByInvoiceNoAndData($invoiceNo, $date);
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
        return $this->invoiceService->getAllowance($invoiceNo, $allowanceNo);
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
        return $this->invoiceService->getInvalid($relateNumber, $invoiceNo, $invoiceDate);
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
        return $this->invoiceService->getAllowanceInvalid($invoiceNo, $allowanceNo);
    }

    /**
     * 查詢字軌
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function getInvoiceWordSetting(array $data): array
    {
        $dataRequiredFields = ['InvoiceYear', 'InvoiceTerm', 'UseStatus'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->invoiceService->getInvoiceWordSetting($data);
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
        $dataRequiredFields = ['InvoiceNo', 'Notify', 'InvoiceTag', 'Notified'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->invoiceService->invoiceNotify($data);
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
        return $this->invoiceService->checkBarcode($barcode);
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
        return $this->invoiceService->checkLoveCode($loveCode);
    }

    /**
     * 發票列印
     *
     * @param array $data
     * @return array
     * @throws InvoiceException
     */
    public function invoicePrint(array $data): array
    {
        return $this->invoiceService->invoicePrint($data);
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
