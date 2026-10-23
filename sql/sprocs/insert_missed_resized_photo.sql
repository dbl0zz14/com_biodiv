DELIMITER $$

-- Stored procedure: insert into bookings, get ID, then insert into booking_details
CREATE PROCEDURE add_missing_resized_photo(
    IN p_olf_id INT,
    IN p_filename VARCHAR(100),
    IN p_size INT,
	OUT p_new_photo_id INT
)
BEGIN
    DECLARE v_photo_id INT;

    -- Insert into Photo
    INSERT INTO Photo (filename, upload_filename, dirname, upload_id, site_id, person_id, taken, size, uploaded, s3_status)
                SELECT p_filename, upload_filename, dirname, upload_id, site_id, person_id, taken, p_size, uploaded, 1
                FROM OriginalLargeFiles
                WHERE olf_id = p_olf_id;

    -- Get the generated booking_id
    SET v_photo_id = LAST_INSERT_ID();
	SET p_new_photo_id = v_photo_id;
	
    -- Insert into PhotoExif using the captured ID
	INSERT INTO PhotoExif (photo_id, exif)
                SELECT v_photo_id, exif
                FROM OriginalLargeFilesExif
                WHERE olf_id = p_olf_id;
				
	INSERT INTO AIQueue (photo_id, ai_type)
            SELECT v_photo_id, 'MEGA'
            WHERE EXISTS (
                SELECT 1 FROM Photo WHERE photo_id = v_photo_id
            );
	
    
END$$

DELIMITER ;