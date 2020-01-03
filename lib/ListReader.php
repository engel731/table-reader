<?php     
    class ListReader extends TableReader 
    {
        protected $_config;
        
        public function __construct($config) {
            parent::__construct($config['src']);

            $this->setConfig($config);

            $this->_reader->setLoadSheetsOnly($this->_config['filter']['sheet-name']);
        }

        public function read() {
            $filter = $this->_config['filter'];
            $data = [];

            foreach ($filter['sheet-name'] as $sheet) { 
                $start_row = $filter['row'][0];                                    
                $end_row   = $filter['row'][1];      
                $highestColumn = end($filter['col']); $highestColumn++; 
        
                $this->_filter->setRange($start_row, $end_row, $filter['col']);
                $spreadsheet = $this->_reader->load($this->_fileName);                                  
                $worksheet = $spreadsheet->getSheetByName($sheet);
                
                for ($row = $start_row; $row <= $end_row; ++$row) { 
                    $col_data = array();

                    for ($col = 'A'; $col != $highestColumn; ++$col) {
                        if (in_array($col, $filter['col'])) { 
                            $value = $worksheet->getCell($col . $row)->getValue();
                            array_push($col_data, $value);
                        }
                    }

                    $row_data = $this->sortRowData([
                        'SHEET-NAME' => $sheet, 
                        'COL' => $col_data
                    ]);

                    array_push($data, $row_data);
                }
            }

            return $data;
        }

        public function setConfig($config) {
            $worksheet = $this->_reader->listWorksheetInfo($this->_fileName)[0];

            if(!array_key_exists('sheet-name', $config['filter'])) {
                if($this->_fileType != 'Csv') {
                    $sheetName = $this->_reader->listWorksheetNames($this->_fileName);
                } else {
                    $sheetName = ['Worksheet'];
                }

                $config['filter']['sheet-name'] = $sheetName;
            }

            if(preg_match('#TOTAL-ROWS#i', $config['filter']['row'][1])) {
                $config['filter']['row'][1] = $worksheet['totalRows'];
            }

            $this->_config = $config;
        }
    }