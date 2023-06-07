<?php

/**
 * 生成随机字符串
 * @param $i
 * @return string
 */
function randStr($i)
{
    $str = "abcdefghijklmnopqrstuvwxyz0123456789";
    $finalStr = "";
    for ($j = 0; $j < $i; $j++) {
        $finalStr .= substr($str, rand(0, 37), 1);
    }
    return $finalStr;
}

/**
 * 发送CURL请求
 * @param $url   请求的URL
 * @param $method   请求方法
 * @param $params  参数（关联数组形式）
 * @param array $header 一维数组的请求头信息（非关联数组）。
 * @return bool
 */
function curl($url, $method = 'GET', $params = [], $header = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //将获取的信息以字符串返回，而不是直接输出
    curl_setopt($ch, CURLOPT_URL, $method == "POST" ? $url : $url . '?' . http_build_query($params));  //http_build_query数组转Url格式参数

    //设置超时时间
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    //如果是https协议，取消检测SSL证书
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //CURL_SSLVERSION_TLSv1
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
    }
    //判断是否设置请求头
    if (count($header) >= 1) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    //通过POST方式提交
    if ($method == "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    }
    $data = curl_exec($ch);   //执行curl操作
    if ($data === false) {
        $data = curl_error($ch);
    }
    curl_close($ch);   //关闭curl操作
    return $data;
}

/**
 * 求两个已知经纬度之间的距离,单位为km
 * @param lat1,lat2 纬度
 * @param lng1,lng2 经度
 * @return float 距离，单位为km
 **/
function getDistance($lat1, $lng1, $lat2, $lng2)
{
//将角度转为狐度
    $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6371;
    return round($s, 1);
}

/**
 * 生成订单号
 * @return string
 */
function getOrderNum()
{
    $orderNumber = date('YmdHis') . substr(uniqid(), 7, 13) . rand(100, 999);
    return $orderNumber;
}

/**
 * 一个字符串向量一维数组和一组字符串向量二维数组对比，把最相似的n个结果输出
 * @param $v1
 * @param $v2
 * @param int $n
 * @return array[]
 */
function findSimilarVectors($v1, $v2, $n = 2)
{
    // 计算v1与每个向量之间的余弦相似度，并保存到一个关联数组中
    $similarities = [];
    foreach ($v2 as $index => $vector) {
        $similarities[$index] = cosineSimilarity($v1, $vector);
    }
    // 按照相似度降序排序
    arsort($similarities);
    // 取最相似的N个向量
    $result = [];
    $indexes = array_keys($similarities);
    $similaritiesResult = [];
    for ($i = 0; $i < $n && $i < count($indexes); $i++) {
        $index = $indexes[$i];
        $result[$index] = $v2[$index];
        $similaritiesResult[] = $index;
    }
    return [$similaritiesResult, $result];
}

/**
 * 计算余弦相似度(入参两个字符串向量数组)
 * @param $v1
 * @param $v2
 * @return float|int
 */
function cosineSimilarity($v1, $v2)
{
    $dot_product = 0;
    $norm_v1 = 0;
    $norm_v2 = 0;
    foreach ($v1 as $key => $value) {
        if (isset($v2[$key])) {
            $dot_product += $v1[$key] * $v2[$key];
        }
        $norm_v1 += pow($v1[$key], 2);
        $norm_v2 += pow(($v2[$key] ?? 0), 2);
    }
    if ($norm_v1 == 0 || $norm_v2 == 0) {
        return 0;
    } else {
        return $dot_product / (sqrt($norm_v1) * sqrt($norm_v2));
    }
}

/**
 * 将中文字符的内容按制定长度切分成二维数组
 */
function contentSlice($str, $len = 500)
{
    $arr = preg_split('/(?<!^)(?!$)/u', $str);
    if ($len == 1) {
        return $arr;
    }
    $strlen = count($arr);
    for ($i = 0; $i < $strlen; $i++) {
        $temp = "";
        for ($j = 0; $j < $len; $j++) {
            if (!empty($arr[$i + $j])) {
                $temp .= $arr[$i + $j];
            }
        }
        if (!empty($temp)) {
            $result[] = $temp;
            $i += $len - 1;
        }
    }
    return $result;
}

/**
 * 获取默认用户对象
 */
function getDefualtUser()
{
    $user_info = ['nickname' => '用户', 'headimgurl' => '/user_headimgurl.png'];
    return json_decode(json_encode($user_info));
}
