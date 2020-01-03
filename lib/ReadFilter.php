<?php     
    use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

    class ReadFilter implements IReadFilter
    {
        private $startRow = 0;
        private $endRow = 0;
        private $columns  = [];

        public function setRange($startRow, $endRow,  $columns) {
            $this->startRow = $startRow;
            $this->endRow = $endRow;
            $this->columns = $columns;
        }

        public function readCell($column, $row, $worksheetName = '') {
            if ($row >= $this->startRow && $row <= $this->endRow) {
                if (in_array($column, $this->columns)) {
                    return true;
                }
            }
            return false;
        }
    }