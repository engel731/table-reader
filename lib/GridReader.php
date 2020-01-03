<?php     
    class GridReader extends TableReader 
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
                $start_row = $filter['row'][0][0];                                  
                $end_row = $filter['row'][0][1];      
                $highestColumn = end($filter['col'][0]); $highestColumn++;

                $this->_filter->setRange($start_row, $end_row, $filter['col'][0]);     
                $spreadsheet = $this->_reader->load($this->_fileName);                               
                $worksheet = $spreadsheet->getSheetByName($sheet);                               
                $coordinates = [];

                for($row = $start_row; $row <= $end_row; ++$row) {
                    for ($col = 'A'; $col != $highestColumn; ++$col) {
                        if (in_array($col, $filter['col'][0])) {                    
                            $value = $worksheet->getCell($col . $row)->getValue();          
                            
                            if(in_array($value, $filter['check-char'])) {                      
                                array_push($coordinates, ['col' => $col, 'row' => $row]);   
                            }
                        }
                    }
                }

                foreach ($coordinates as $coordinate) {
                    $start_row = $filter['row'][1][0];
                    $end_row = $filter['row'][1][1];

                    $start_col = $filter['col'][1][0];
                    $end_col = end($filter['col'][1]); $end_col++;

                    $col_data = array();
                    $cpt = 0;
                    
                    for($i = $start_row; $i <= $end_row; $i++) {
                        $value = $worksheet->getCell($coordinate['col'] . $i)->getValue();
                        array_push($col_data, $value);

                        $cpt++;
                    }
                
                    for($c = $start_col; $c != $end_col; $c++) {
                        $value = $worksheet->getCell($c . $coordinate['row'])->getValue();
                        array_push($col_data, $value);
                        
                        $cpt++;
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

            if(preg_match('#TOTAL-ROWS#i', $config['filter']['row'][0][1])) {
                $config['filter']['row'][0][1] = $worksheet['totalRows'];
            }
            
            $this->_config = $config;
        }
    }