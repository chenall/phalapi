<?php
namespace Chenall\PhalApi;

use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Reader\Csv;
use \PhpOffice\PhpSpreadsheet\Reader\Xls;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;

class Excel
{
    private $maxCol = 0;
    private $maxRow = 0;
    /**
     * @var Worksheet
     */
    private $Sheet;
    private $currentRow = 1;
    /** @var \PhpOffice\PhpSpreadsheet\Spreadsheet */
    private $Spreadsheet;
    private $sheetCount;


    public function load($file, $tmpfile = false)
    {
        $reader = null;
        if ($tmpfile) {
            $reader = $this->CreateReader($file['name']);
            $file = $file['tmp_name'];
        } else $reader = $this->CreateReader($file);
        if (empty($reader)) return false;
        $Spreadsheet = $reader->load($file);
        $this->sheetCount = $Spreadsheet->getSheetCount();
        $this->Spreadsheet = $Spreadsheet;
        $this->Sheet = $Spreadsheet->getActiveSheet();
        $this->maxCol = $this->Sheet->getHighestColumn();
        $this->maxRow = $this->Sheet->getHighestRow();
        return $this;
    }
    /**
     * Create Reader\IReader.
     *
     * @param string $readerType Example: Xlsx
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     */
    private function CreateReader($filename)
    {
        $pathinfo = pathinfo($filename);
        if (!isset($pathinfo['extension'])) {
            return null;
        }
        switch (strtolower($pathinfo['extension'])) {
            case 'xlsx': // Excel (OfficeOpenXML) Spreadsheet
            case 'xlsm': // Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            case 'xltx': // Excel (OfficeOpenXML) Template
            case 'xltm': // Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                return IOFactory::createReader('Xlsx');
            case 'xls': // Excel (BIFF) Spreadsheet
            case 'xlt': // Excel (BIFF) Template
                return IOFactory::createReader('Xls');
            default:
                return null;
        }
    }

    /**
     * 获取当前工作表
     *
     * @return Worksheet
     */
    public function getSheet()
    {
        return $this->Sheet;
    }

    /**
     * 设置当前工作表
     *
     * @return Worksheet
     */
    public function setSheet($id)
    {
        if ($id >= 0 && $id < $this->sheetCount) {
            $sheet = $this->Spreadsheet->setActiveSheetIndex($id);
            $this->currentRow = 1;
            $this->maxCol = $sheet->getHighestColumn();
            $this->maxRow = $sheet->getHighestRow();
            return $this->Sheet = $sheet;
        }
        return null;
    }

    /**
     * 设置表格读取的最大列
     * 
     */
    public function setMaxCol($col)
    {
        $this->maxCol = $col;
        return $this;
    }

    /**
     * 读取当前表格的一行
     *
     * @param integer $row  行号,等于0是自动读取下一行
     * @param boolean|array $formatData 自定义列格式.
     * @return false|array 失败时返回false,否则返回数组.
     */
    public function readLine($row = 0, $formatData = true)
    {
        if ($row == 0) $row = $this->currentRow;
        if ($row > $this->maxRow) return false;
        $data = $this->Sheet->rangeToArray('A' . $row . ':' . $this->maxCol . $row, null, true, $formatData === true, true);
        if (is_array($formatData)) {
            foreach ($formatData as $n => $v) {
                $data[$row][$n] = NumberFormat::toFormattedString($data[$row][$n], $v);
            }
        }
        $this->currentRow = $row + 1;
        return $data[$row];
    }
}
