<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ignore_Get_next_id extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            DROP FUNCTION IF EXISTS GET_NEXT_ID;
            DELIMITER $$
            CREATE FUNCTION GET_NEXT_ID (sSeqName VARCHAR(50)) RETURNS varchar(50)
            BEGIN
                DECLARE nLast_val INT; 
                DECLARE nGroup CHAR(5);
                SET nGroup =  (SELECT seq_group
                                    FROM _sequence
                                    WHERE seq_name = sSeqName);
                
                SET nLast_val =  (SELECT seq_val
                                    FROM _sequence
                                    WHERE seq_name = sSeqName);
                IF nLast_val IS NULL THEN
                    SET nLast_val = 1;
                    UPDATE _sequence SET seq_val = nLast_val
                    WHERE sSeqName = sSeqName;
                ELSE
                    SET nLast_val = nLast_val + 1;
                    UPDATE _sequence SET seq_val = nLast_val
                    WHERE sSeqName = sSeqName;
                END IF; 
            
                SET @ret = (SELECT CONCAT(LPAD(MONTH(CURRENT_DATE()),2,'0'), '/', YEAR(CURRENT_DATE()), '/', nGroup,'-', LPAD(nLast_val, 8, '0')) AS order_no);
                RETURN @ret;
            END;
            $$
            DELIMITER ;"
        );
    }
}