<?php

    /**
     * XSS対策: エスケープ処理
     * @param string $str
     * @return string 変換した文字列
     */
    function h($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
    }

    /**
     * CSRF対策
     * @param void
     * @return string $csrf_token
     */
    function set_token()
    {
        $csrf_token = bin2hex(openssl_random_pseudo_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        return $csrf_token;
    }