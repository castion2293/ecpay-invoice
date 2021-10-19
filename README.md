# 綠界B2C開發票工具

## 安裝
使用 composer 做安裝
```bash
composer require thoth-pharaoh/ecpay-invoice
```

## 匯出 Config
```bash
php artisan vendor:publish --tag=invoice-config --force
```

## 添加 .env 支付工具必要環境參數
```bash
INVOICE_URL="https://einvoice-stage.ecpay.com.tw/B2CInvoice/"
INVOICE_MERCHANT_ID="2000132"
INVOICE_HASH_KEY="ejCk326UnaZWKisg"
INVOICE_HASH_IV="q9jcZX8Ib9LM8wYk"
INVOICE_VISION="3.0.0"
```

## 使用方法

### 先引入門面
```bash
use Pharaoh\Invoice\Facades\Invoice;
```

### 開立一般發票
```bash
$invoice = Invoice::issue($data);
```

#### $data 內容說明(array格式)

參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| RelateNumber |✔| 特店自訂編號 | String (30) | 需為唯一值不可重複使用(請勿使用特殊符號) |
| Print |✔| 列印註記 | String (1) | 0:不列印 <br> 1:要列印 <br> 注意事項: <br> 1. 當捐贈註記[Donation]=1(要捐贈)或載具類別[CarrierType]有值時，此參數請帶0 <br>2. 當統一編號[CustomerIdentifier]有值時，此參數請帶 1 |
| Donation |✔| 特店自訂編號 | String (1) | 0:不捐贈 <br> 1:要捐贈 <br> 注意事項: <br> 當統一編號[CustomerIdentifier]有值或載具類別[CarrierType]有值 時，此參數請帶 0 |
| TaxType |✔| 課稅類別 | String (1) | 當字軌類別[InvType]為 07 時，則此欄位請填入 1、2、3 或 9 <br> 當字軌類別[InvType]為 08 時，則此欄位請填入 3 或 4 <br> 1:應稅。 <br> 2:零稅率。 <br> 3:免稅。 <br> 4:應稅(特種稅率) <br> 9:混合應稅與免稅或零稅率時(限收銀機發票無法分辨時使用，且 需通過申請核可)
| SalesAmount |✔| 發票總金額(含 稅) | int | 請帶整數，不可有小數點 <br> 僅限新台幣 <br> 金額不可為 0 元 |
| InvType |✔| 字軌類別 | String (2) | 該張發票的發票字軌類型 <br> 07:一般稅額 <br> 08 : 特種稅額 |
| CustomerID | | 客戶編號 | String (20) | 格式為『英文、數字、下底線』等字元 |
| CustomerIdentifier | | 統一編號 | String (8) | 格式為數字 |
| CustomerAddr | | 客戶地址 | String (100) | 當列印註記[Print]=1(列印)時，為必填 |
| CustomerPhone | | 客戶手機號碼 | String (20) | 當客戶電子信箱[CustomerEmail]為空字串時，為必填 <br> 格式為數字 |
| CustomerEmail | | 客戶電子信箱 | String (80) | 當客戶手機號碼[CustomerPhone]為空字串時，為必填 <br> 需為有效的 Email 格式，且僅可填寫一組 Email <br> 注意事項: <br> 1.測試環境請勿帶入之真實電子信箱，避免個資外洩 <br> 2.測試環境僅作 API 串接測試使用，僅以 API 回覆成功或失敗;批 次匯入功能/API 不提供發信測試，僅驗規則 |
| ClearanceMark | | 通關方式 | String (1) | 當課稅類別[TaxType]=2(零稅率)時，為必填 <br> 1:非經海關出口 <br> 2:經海關出口 |
| LoveCode | | 捐贈碼 | String (7) | 當捐贈註記[Donation]=1(要捐贈)時，為必填 <br> 格式為阿拉伯數字為限，最少三碼，最多七碼，首位可以為零 |
| CarrierType | | 載具類別 | String (1) | 空字串:無載具 <br> 1:綠界電子發票載具 <br> 2:自然人憑證號碼 <br> 3:手機條碼載具 <br> 注意事項: <br> 1. 當列印註記[Print] =1(要列印)或統一編號[CustomerIdentifier]有值時，請帶空字串 <br> 2.只有存在綠界電子發票載具(此參數帶 1)的發票，中獎後才能在 ibon 列印領取 |
| CarrierNum | | 載具編號 | String (64) | 當[CarrierType]="" 時，請帶空字串 <br> 當[CarrierType]=1 時，請帶空字串，系統會自動帶入值，為客戶電 子信箱或客戶手機號碼擇一(以客戶電子信箱優先) <br> [CarrierType]=2:請帶固定長度為 16 且格式為 2 碼大寫英文字母加 上 14 碼數字 <br> [CarrierType]=3:請帶固定長度為 8 碼字元，第 1 碼為【/】; 其餘 7 碼則由數字【0-9】、大寫英文【A-Z】與特殊符號【+】【-】【.】這 39 個字元組成的編號 |
| SpecialTaxType | | 特種稅額類別 | int | 當課稅類別[TaxType]為 1/2/9 時，系統將會自動帶入數字【0】<br> 當課稅類別[TaxType]為 3 時，則該參數必填，請填入數字【8】 <br> 當課稅類別[TaxType]為 4 時，則該參數必填，可填入數字【1-8】， 並分別代表以下類別與稅率 <br> 1:代表酒家及有陪侍服務之茶室、咖啡廳、酒吧之營業稅稅率， 稅率為 25% <br> 2:代表夜總會、有娛樂節目之餐飲店之營業稅稅率，稅率為 15% <br> 3:代表銀行業、保險業、信託投資業、證券業、期貨業、票券業 及典當業之專屬本業收入(不含銀行業、保險業經營銀行、保險本 業收入)之營業稅稅率，稅率為 2% <br> 4:代表保險業之再保費收入之營業稅稅率，稅率為 1% <br> 5:代表銀行業、保險業、信託投資業、證券業、期貨業、票券業及典當業之非專屬本業收入之營業稅稅率，稅率為 5% <br> 6:代表銀行業、保險業經營銀行、保險本業收入之營業稅稅率(適 用於民國 103 年 07 月以後銷售額) ，稅率為 5% <br> 7:代表銀行業、保險業經營銀行、保險本業收入之營業稅稅率(適 用於民國 103 年 06 月以前銷售額) ，稅率為 5% <br> 8:代表空白為免稅或非銷項特種稅額之資料 |
| InvoiceRemark | | 發票備註 | String (200) |  |
| vat | | 商品單價是否 含稅 | String (1) | 預設為含稅價 <br> 1:含稅 <br> 0:未稅 |
| Items | | 商品 | array | 可多筆，商品最多支援 200 項 請參閱下面 Items 詳細說明 |

#### Items 參數說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| ItemName |✔| 商品名稱 | String (100) |  |
| ItemCount |✔| 商品數量 | Number | 支援整數 8 位小數 2 位 |
| ItemWord |✔| 商品單位 | String (6) |  |
| ItemPrice |✔| 商品單價 | Number | 支援整數 8 位小數 7 位 <br> 若 vat=0(未稅)，商品金額需為未稅金額 <br> 若 vat=1(含稅)，商品金額需為含稅金額 |
| ItemAmount |✔| 商品合計 | Number | 支援整數 8 位小數 7 位 <br> 此為含稅小計金額 <br> ItemAmount 各項總合並四捨五入=salesAmount(含稅) <br> 注意事項: <br> ※ItemAmount 需統一為含稅金額，且商品金額需符合以下規則: <br> 1. 當 vat = 1, 且 TaxType = 1 或 4: ItemPrice(含稅)*ItemCount = ItemAmount(含稅)ex: 500*5 = 2500 <br> 2. 當 vat = 0,且 TaxType = 1(稅率 5%): ItemPrice(不含稅)*ItemCount*1.05 = ItemAmount(含稅) ex: 500*5*1.05 = 2625 <br> 3. 當 vat = 0, TaxType = 4 且 ex: 500*5*1.00 = 2500 |
| ItemSeq | | 商品序號 | Int |  |
| ItemTaxType | | 商品課稅別 | String (1) | 當課稅類別[TaxType] = 9 時，此欄位不可為 <br> 1:應稅 <br> 2:零稅率 <br> 3:免稅 <br> 注意事項: <br> 當課稅類別[TaxType] = 9 時，商品課稅類別只能 1.應稅+免稅 2.應 稅+零稅率，免稅和零稅率發票不能同時開立 |
| ItemRemark | | 商品備註 | String (40) |  |

### 開立延遲發票
```bash
$invoice = Invoice::delayIssue($data);
```

#### $data 內容說明(array格式)
大部分與上列 `開立一般發票` 相同 額外增加欄位如下:

參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| DelayFlag |✔| 延遲註記 | String (1) | 可註記此張發票要延遲開立或觸發開立發票 <br> 1:延遲開立 <br> 2:觸發開立 |
| DelayDay |✔| 延遲天數 | int | 若為延遲開立時，延遲天數須介於 1 至 15 天內 <br> 觸發開立時也可設定延遲天數，但須介於 0 至 15 天內 <br> EX1: <br> DelayFlag=1(延遲) <br> DelayDay=7(天數) <br> 此為 7 天後自動開立 <br> EX2: <br> DelayFlag = 2(觸發) <br> DelayDay=2(天數) <br> 此為被觸發後過 2 天才會開立，若此張發票都沒有被觸發，將不會被開立 |
| Tsr |✔| 交易單號 | String (30) | 用來呼叫付款完成觸發或延遲開立發票 API 的依據 <br> 均為唯一值不可重覆使用 |
| NotifyURL | | 開立完成時通知特店系統的網址 | String (200) | 注意事項: <br> 使用測試環境時，不提供 NotifyURL 開立通知 |

### 觸發開立發票
```bash
$invoice = Invoice::delayIssue($transactionNumber);
```

#### $transactionNumber 內容說明
參數 |  名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------|
$transactionNumber | 交易單號 | string | 觸發開立發票回傳的交易單號 |

### 取消延遲開立發票
```bash
$invoice = Invoice::cancelDelayIssue($transactionNumber);
```

#### $transactionNumber 內容說明
參數 |  名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------|
$transactionNumber | 交易單號 | string | 觸發開立發票回傳的交易單號 |

### 開立一般折讓發票
```bash
$invoice = Invoice::allowance($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| InvoiceNo |✔| 發票號碼 | String (10) | 長度固定為 10 碼 |
| InvoiceDate |✔| 發票開立日期 | String (10) | 格式為「yyyy-MM-dd」 |
| AllowanceNotify |✔| 通知類別 | String (1) | 開立折讓後，寄送將相關發票折讓資訊通知消費者 <br> S:簡訊 <br> E:電子郵件 <br> A:皆通知時 <br> N:皆不通知 |
| AllowanceAmount |✔| 折讓單總金額(含稅) | int |  |
| CustomerName | | 客戶名稱 | String (60) | 格式為中、英文及數字等 |
| NotifyMail | | 通知電子信箱 | String (100) | 1. 若通知類別[AllowanceNotify]為電子郵件(E)，此欄位須有值 <br> 2. 需為有效的 Email 格式 <br> 3. 將參數值做 UrlEncode <br> 4. 可帶入多組 Email，並以分號區隔 ex: aa@aa.aa;bb@bb.bb |
| NotifyPhone | | 通知手機號 碼 | String (20) | 1. 若通知類別[AllowanceNotify]為簡訊方式(S)，此欄位須有值 <br> 2. 格式為數字組成 |
| Items | | 商品 | array | 可多筆，商品最多支援 200 項 請參閱下面 Items 詳細說明 |

#### Items 參數說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| ItemName |✔| 商品名稱 | String (100) |  |
| ItemCount |✔| 商品數量 | Number | 支援整數 8 位小數 2 位 |
| ItemWord |✔| 商品單位 | String (6) |  |
| ItemPrice |✔| 商品單價 | Number | 支援整數 8 位小數 7 位 <br> 若 vat=0(未稅)，商品金額需為未稅金額 <br> 若 vat=1(含稅)，商品金額需為含稅金額 |
| ItemAmount |✔| 商品合計 | Number | 支援整數 8 位小數 7 位 <br> 此為含稅小計金額 <br> ItemAmount 各項總合並四捨五入=salesAmount(含稅) <br> 注意事項: <br> ※ItemAmount 需統一為含稅金額，且商品金額需符合以下規則: <br> 1. 當 vat = 1, 且 TaxType = 1 或 4: ItemPrice(含稅)*ItemCount = ItemAmount(含稅)ex: 500*5 = 2500 <br> 2. 當 vat = 0,且 TaxType = 1(稅率 5%): ItemPrice(不含稅)*ItemCount*1.05 = ItemAmount(含稅) ex: 500*5*1.05 = 2625 <br> 3. 當 vat = 0, TaxType = 4 且 ex: 500*5*1.00 = 2500 |
| ItemSeq | | 商品序號 | Int |  |
| ItemTaxType | | 商品課稅別 | String (1) | 當課稅類別[TaxType] = 9 時，此欄位不可為 <br> 1:應稅 <br> 2:零稅率 <br> 3:免稅 <br> 注意事項: <br> 當課稅類別[TaxType] = 9 時，商品課稅類別只能 1.應稅+免稅 2.應 稅+零稅率，免稅和零稅率發票不能同時開立 |
| ItemRemark | | 商品備註 | String (40) |  |

### 開立線上折讓發票(通知開立)
```bash
$invoice = Invoice::allowanceByCollegiate($data);
```

#### $data 內容說明(array格式)
各式與 `開立一般折讓發票` 皆相同

### 作廢發票
```bash
$invoice = Invoice::invalid($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| InvoiceNo |✔| 發票號碼 | String (10) | 長度固定為 10 碼 |
| InvoiceDate |✔| 發票開立日期 | String (10) | 格式為「yyyy-MM-dd」 |
| Reason |✔| 作廢原因 | String (120) |  |

### 作廢折讓發票
```bash
$invoice = Invoice::allowanceInvalid($data);
```
#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| InvoiceNo |✔| 發票號碼 | String (10) | 長度固定為 10 碼 |
| AllowanceNo |✔| 折讓編號 | String (16) |  |
| Reason |✔| 作廢原因 | String (120) |  |

### 註銷重開
```bash
$invoice = Invoice::voidWithReIssue($data);
```

#### $data 內容說明(array格式)
大部分與上列 `開立一般發票` 相同 額外增加欄位如下:

參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| InvoiceNo |✔| 發票號碼 | String (10) | 長度固定為 10 碼 |
| VoidReason |✔| 註銷原因 | String (20) |  |
| InvoiceDate |✔| 發票開立時間 | String (20) | 格式為 yyyy-MM-dd HH:mm:ss <br> 發票開立時間需為先前開立發票的時間 |

### 查詢發票(根據特店自訂編號)
```bash
$invoice = Invoice::getIssueByRelateNumber($relateNumber);
```

#### $relateNumber 內容說明
參數 |  名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------|
$relateNumber | 特店自訂編號 | string(30) | 需為唯一值不可重複使用 <br> 注意事項: <br> 請勿使用特殊符號 |

### 查詢發票(根據發票號及開立日期)
```bash
$invoice = Invoice::getIssueByInvoiceNoAndData($invoiceNo, $invoiceDate);
```

#### $invoiceNo 及 $invoiceDate 內容說明
參數 |  名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------|
| InvoiceNo |✔| 發票號碼 | String (10) |  |
| InvoiceDate |✔| 發票開立日期 | String (10) | 格式為「yyyy-MM-dd」 |

### 查詢折攘明細
```bash
$invoice = Invoice::getAllowance($invoiceNo, $allowanceNo);
```

#### $invoiceNo 及 $allowanceNo 內容說明
參數 |  名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------|
| InvoiceNo |✔| 發票號碼 | String (10) |  |
| AllowanceNo |✔| 折讓編號 | String (16) |  |

### 查詢作廢發票明細
```bash
$invoice = Invoice::getInvalid($relateNumber, $invoiceNo, $invoiceDate);
```

#### $relateNumber, $invoiceNo 及 $invoiceDate 內容說明
參數 |  名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------|
| $relateNumber |✔| 特店自訂編號 | String (10) | 需為唯一值不可重複使用 <br> 注意事項: <br> 請勿使用特殊符號 |
| InvoiceNo |✔| 發票號碼 | String (10) |  |
| InvoiceDate |✔| 發票開立日期 | String (10) | 格式為「yyyy-MM-dd」 |

### 查詢作廢折讓明細
```bash
$invoice = Invoice::getAllowanceInvalid($invoiceNo, $allowanceNo);
```

#### $invoiceNo 及 $allowanceNo 內容說明
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| InvoiceNo |✔| 發票號碼 | String (10) | 長度固定為 10 碼 |
| AllowanceNo |✔| 折讓編號 | String (16) |  |

### 查詢字軌
```bash
$invoice = Invoice::getInvoiceWordSetting($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| InvoiceYear |✔| 發票年度 | String (3) | 僅可查詢去年、當年與明年的發票年度，格式為民國年 ex:109 |
| InvoiceTerm |✔| 發票期別 | int | 0:全部，1: 1-2 月，2: 3-4 月，3: 5-6 月，4: 7-8 月，5: 9-10 月，6: 11-12月 |
| UseStatus |✔| 字軌使用狀態 | int | 0:全部，1:未啟用，2:使用中，3:已停用，4:暫停中，5:待審核，6: 審核不通過 |
| InvType | | 字軌類別 | String (2) | 07:一般稅額發票，08:特種稅額發票 |
| InvoiceHeader | | 字軌名稱 | String (2) | |
