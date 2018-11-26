<?php
namespace Files/Csv;
class Csv
{
    public static $instance;
    private $bom;

    private function __construct()
    {
        $this->bom = chr(0xEF).chr(0xBB).chr(0xBF);// utf8的bom
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getBom()
    {
        return $this->bom;
    }

    /**
     * @param $data 是二维数组
     * @param string $fileName 文件名
     */
    public function fileLoad(array $data,$fileName='')
    {
        ini_set('memory_limit', '1024M');
        $bom = $this->getBom();
        $fileName = $fileName ?: '导出数据-'.date('Y-m-d', time());
//设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$fileName.'.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        fwrite($fp,$bom);// 写入bom
        foreach($data as $key => $value) {
            if (!is_array($value)) {
                $value = [$value];
            }
            if (count($value) != count($value,1)){ // 如果不是一维数组，就跳过写入数据
                continue;
            }
            fputcsv($fp, $value);
        }
        //释放变量的内存
        unset($data,$value);
        //刷新输出缓冲到浏览器
        ob_flush();
        //必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
        flush();
        fclose($fp);
        exit();
    }
}