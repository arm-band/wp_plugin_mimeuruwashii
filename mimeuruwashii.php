<?php
/*
Plugin Name: MIMEuruwashii
Description: PHP の MIMEタイプ 判定で .zipファイル や .mdファイル, 特定の .xlsファイル が弾かれる現象への対策
Version: 0.0.4
Author: アルム＝バンド
*/

/**
 * MIMEuruwashii : PHP の MIMEタイプ 判定で .zipファイル や .mdファイル, 特定の .xlsファイル が弾かれる現象への対策
 */
class MIMEuruwashii
{
    /**
     * __construct : コンストラクタ
     *
     */
     public function __construct() {
        add_filter(
            'wp_check_filetype_and_ext',
            [
                $this,
                'Bibishii',
            ],
            99,
            3
        );
    }
    /**
     * Vivid                     : 追加する MIMEタイプ の定義
     *
     * @return {Object}          : 誤判定している MIME タイプを追記する
     *
     */
    public static function Vivid(){
        return [
            [
                'xla|xls|xlt|xlw' => 'application/vnd.ms-office'
            ],
            [
                'xla|xls|xlt|xlw' => 'application/vnd.ms-excel'
            ],
            [
                'md|markdown' => 'text/plain'
            ],
            [
                'zip|xzip' => 'application/zip'
            ],
            [
                'zip|xzip' => 'application/z-zip'
            ],
            [
                'zip|xzip' => 'application/zip-compressed'
            ],
            [
                'zip|xzip' => 'application/x-zip-compressed'
            ],
            [
                'zip|xzip' => 'application/compressed'
            ],
            [
                'zip|xzip' => 'application/x-compressed'
            ],
            [
                'zip|xzip' => 'application/octet'
            ],
            [
                'zip|xzip' => 'application/octet-stream'
            ],
        ];
    }
    /**
     * Bibishii                  : フィルター追加
     *
     * @param {Object} $check    : MIMEタイプ の一覧のオブジェクト
     * @param {File} $file       : チェック対象ファイル
     * @param {Object} $filename : チェック対象ファイルの名前
     *
     * @return {Object}          : 誤判定している MIME タイプを追記する
     *
     */
    public function Bibishii ( $check, $file, $filename )
    {
        if ( empty( $check['ext'] ) && empty( $check['type'] ) ) {
            foreach ( self::Vivid() as $mime ) {
                remove_filter(
                    'wp_check_filetype_and_ext',
                    [
                        $this,
                        'Bibishii'
                    ],
                    99
                );
                $mime_filter = function($mimes) use ($mime) {
                    return array_merge($mimes, $mime);
                };

                add_filter(
                    'upload_mimes',
                    $mime_filter,
                    99
                );
                $check = wp_check_filetype_and_ext( $file, $filename, $mime );
                remove_filter(
                    'upload_mimes',
                    $mime_filter,
                    99
                );
                add_filter(
                    'wp_check_filetype_and_ext',
                    [
                        $this,
                        'Bibishii'
                    ],
                    99,
                    3
                );
                if ( ! empty( $check['ext'] ) || ! empty( $check['type'] ) ) {
                    return $check;
                }
            }
        }

        return $check;
    }
}

// instantiate
$ab_wp_plugin_mimeuruwashii = new MIMEuruwashii();
