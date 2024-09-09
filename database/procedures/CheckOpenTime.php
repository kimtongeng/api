<?php

namespace Database\Procedures;

class CheckOpenTime {
    const code = "
        CREATE FUNCTION CheckOpenTime(openTime VARCHAR(20), closeTime VARCHAR(20), curTime VARCHAR(20))
        RETURNS BOOL
        BEGIN
            DECLARE isShow bool;
            IF curTime is null THEN
                SET curTime = TIME(NOW());
            END IF;
            -- IF Open time < Close time
            IF openTime = '00:00:00' THEN
                SET openTime = '23:59:59';
            END IF;

            IF closeTime = '00:00:00' THEN
                SET closeTime = '23:59:59';
            END IF;

            IF TIME(openTime) < TIME(closeTime) THEN
                IF TIME(curTime) >= TIME(openTime) AND TIME(curTime) <= TIME(closeTime) THEN
                    SET isShow = TRUE;
                ELSE
                    SET isShow = FALSE;
                END IF;
            ELSE
                IF
                    (curTime >= TIME(openTime) AND curTime <= TIME('23:59:59')) OR
                    (curTime >= TIME('23:59:59') AND curTime <= TIME(closeTime))
                THEN
                    SET isShow = true;
                ELSE
                    SET isShow = false;
                END IF;
            END IF;

            RETURN isShow;
        END;
    ";

    const drop = 'DROP FUNCTION IF EXISTS CheckOpenTime;';
}
