<?php

namespace Curler;

class Curler{

    /**
     * cURL リソース
     *
     */
    private $ch;

    /**
     * 接続先URL
     *
     * @var string
     */
    public $url;

    /**
     * HTTPヘッダー
     * 変更 : この変数に代入。
     * 追加 : Curler::addHeader(追加するヘッダー配列)
     *
     * @var array
     */
    public $header;

    /**
     * CURLINFO_HEADER_OUT オプション
     * true : curl_getinfoにてリクエストヘッダを取得する
     *
     * @var boolean
     */
    public $infoHeaderOut;

    /**
     * CURLOPT_AUTOREFERER オプション
     * true : 接続先にLocationヘッダが存在した(リダイレクト処理が行われた)場合、
     *        リクエストヘッダにRefererヘッダを付加する。
     *
     * @var boolean
     */
    public $optAutoReferer;

    /**
     * CURLOPT_FOLLOWLOCATION オプション
     * true : リダイレクトをたどる。再帰的に実行される。
     *
     *
     * @var boolean
     */
     public $optFollowLocation;

     /**
      * CURLOPT_MAXREDIRS オプション
      * CURLOPT_FOLLOWLOCATION オプションがtrueの場合のリダイレクトの最大数
      *
      * @var integer
      */
     public $optMaxRedirs;

    /**
     * CURLOPT_RETURNTRANSFER オプション
     * true : curl_exec()の返り値を文字列で返します。
     *        通常はデータを直接出力。
     *
     * @var boolean
     */
    public $optReturnTransfer;

    /**
     * CURLOPT_SSL_VERIFYPEER オプション
     * false : SSLサーバー証明書の検証を行わない
     *
     *
     * @var boolean
     */
    public $optSSLVerifypeer;

    /**
     * CURLOPT_CONNECTTIMEOUT オプション
     * 接続の試行を待ち続ける秒数。0 は永遠に待ち続けることを意味します。
     *
     *
     * @var integer
     */
    public $optConnectTimeout;

    /**
     * CURLOPT_TIMEOUT オプション
     * cURL 関数の実行にかけられる時間の最大値
     *
     *
     * @var integer
     */
    public $optTimeout;

    /**
     * curl_execで取得したデータ
     *
     *
     * @var boolean CURLOPT_RETURNTRANSFERオプションが設定されていた場合はstring or false
     */
    public $response;

    /**
     * curl_getinfoの返り値
     *
     *
     * @var array
     */
    public $info;

    /**
     * コンストラクタ
     * curlハンドルを$this->chに格納
     *
     * @return void
     */
    public function __construct()
    {
        $this->ch = curl_init();

        $this->url = '';

        $this->header = [
                'Accept: ' .
                    'text/html,' .
                    'application/xhtml+xml,' .
                    'application/xml' .
                    ';q=0.9,*/*;q=0.8'
                ,
                'Accept-Language: ' .
                    'ja,en-us;q=0.7,en;q=0.3'
                ,
        ];

        $this->infoHeaderOut = true;

        $this->optAutoReferer = true;

        $this->optFollowLocation = true;

        $this->optMaxRedirs = 5;

        $this->optReturnTransfer = true;

        $this->optSSLVerifypeer = false;

        $this->optConnectTimeout = 10;

        $this->optTimeout = 15;

        $this->response = false;

        $this->info = false;
    }

    /**
     * HTTP GET通信
     *
     * @return void
     */
    public function get()
    {
        $this->addOptions(
             [
                  CURLOPT_URL => $this->url,
                  CURLOPT_HTTPGET => true,
              ]
          );

        $this->setOptions();

        $this->process();
    }

    /**
     * HTTP POST通信
     *
     * @return void
     */
    public function post($field, $json = false)
    {
        if (!$json) {
            $field = http_build_query($field);
        }

        $this->addOptions(
              [
                  CURLOPT_URL => $this->url,
                  CURLOPT_POST => true,
                  CURLOPT_POSTFIELDS => $field,
              ]
          );

        $this->setOptions();

        $this->process();
    }

    /**
     * 接続
     *
     * @return void
     */
    private function process()
    {
        $this->response = curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);
    }

    /**
     * curlオプションをセット
     *
     * @return void
     */
    protected function setOptions()
    {
        curl_setopt_array($this->ch,
            [
                CURLINFO_HEADER_OUT => $this->infoHeaderOut,
                CURLOPT_AUTOREFERER => $this->optAutoReferer,
                CURLOPT_FOLLOWLOCATION => $this->optFollowLocation,
                CURLOPT_RETURNTRANSFER => $this->optReturnTransfer,
                CURLOPT_SSL_VERIFYPEER => $this->optSSLVerifypeer,
                CURLOPT_CONNECTTIMEOUT => $this->optConnectTimeout,
                CURLOPT_TIMEOUT => $this->optTimeout,
                CURLOPT_MAXREDIRS => $this->optMaxRedirs,
                CURLOPT_HTTPHEADER => $this->header
            ]
        );
    }

    /**
     * HTTPヘッダーを追加
     *
     * @param array $addHeader
     * @return void
     */
    public function addHeader($addHeader)
    {
        $this->header = array_merge($this->header, $addHeader);
    }

    /**
     * curlオプションを追加でセット
     *
     * @param array $options
     * @return void
     */
    public function addOptions($options)
    {
        curl_setopt_array($this->ch, $options);
    }


    public function __destruct() {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }

}
