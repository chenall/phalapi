<?php
namespace Chenall\PhalApi;

use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Reader\Csv;
use \PhpOffice\PhpSpreadsheet\Reader\Xls;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Excel
{
    private $maxCol = 0;
    private $maxRow = 0;
    /**
	 * @var Worksheet
	 */
    private $Sheet;
    private $currentRow = 1;


    public function load($file, $tmpfile = false)
    {
        $reader = null;
        if ($tmpfile) {
            $reader = $this->CreateReader($file['name']);
            $file = $file['tmp_name'];
        } else $reader = $this->CreateReader($file);
        if (empty($reader)) return false;
        $Spreadsheet = $reader->load($file);
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

    public function readLine($row = 0)
    {
        if ($row == 0) $row = $this->currentRow;
        if ($row > $this->maxRow) return false;
        $data = $this->Sheet->rangeToArray('A' . $row . ':' . $this->maxCol . $row, null, true, true, true);
        $this->currentRow = $row + 1;
        return $data[$row];
    }
}
