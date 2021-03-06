<?php    
    namespace TableReader\Config;
    
    class CharRange 
    {
        private static function convert_to_numb($str) { //Max ZZ
            $str_len = strlen($str) - 1;  
            
            $alphabet = range('A', 'Z');
            $alphabet_len = count($alphabet);
            $number = 0;

            for($i = 0; $i < $str_len; $i++) {
                $key = array_search($str[$i], $alphabet);
                $number += ($key + 1) * $alphabet_len;
            }

            $key = array_search($str[$str_len], $alphabet);
            return $number += ($key + 1);
        }
        
        private static function convert_to_char($num) { //Max 702 
            $num -= 1; 

            $alphabet = range('A', 'Z');
            $alphabetLen = count($alphabet);
            $lettre_first = '';
        
            for($i = 0; $num >= $alphabetLen; $i++) {
                $num -= $alphabetLen;
                $lettre_first = $alphabet[$i];
            }

            if($num < $alphabetLen) {
                return $lettre_first . $alphabet[$num];
            }
        }

        private static function convert_range_to_char($range) {
            return array_map(function($numb) { 
                return self::convert_to_char($numb); 
            }, $range);
        }

        public static function char_range($start, $end) {
            $start_numb = self::convert_to_numb($start);
            $end_numb = self::convert_to_numb($end);

            $range_numb = range($start_numb, $end_numb);
            return self::convert_range_to_char($range_numb);
        }
    }