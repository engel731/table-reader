<?php 
    namespace TableReader;
    
    use PhpOffice\PhpSpreadsheet\IOFactory;

    abstract class TableReader
    {
        protected $_fileName;
        protected $_fileType;
        
        protected $_render;
        protected $_filter;

        public function __construct($fileName) 
        {
            $this->setFileName($fileName);

            $this->_reader = IOFactory::createReader($this->_fileType); 
            $this->_reader->setReadDataOnly(TRUE); 

            $this->_filter = new ReadFilter();
            $this->_reader->setReadFilter($this->_filter);
        }

        abstract public function read();

        public function sortRowData($row) {
            $data = [];

            foreach($this->_config['sort'] as $col => $var) {
                if(!is_array($var)) {
                    $value = $row[$var]; 
                } else {
                    $key = array_keys($var)[0];
                    $index = $var[$key];
                    
                    $value = $row[$key][$index];
                }
                
                if(array_key_exists('action', $this->_config)) {
                    if(array_key_exists($col, $this->_config['action'])) {
                        $action = $this->_config['action'][$col];
                        $value = $action($value);
                    }
                }

                $data[$col] = $value;
            }

            return $data;
        }

        public function setFileName($fileName) 
        {
            $this->_fileName = $fileName;
            $this->_fileType = IOFactory::identify($fileName); 
        }
    }