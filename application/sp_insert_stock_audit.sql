DELIMITER $$

CREATE PROCEDURE `sp_insert_stock_audit` ( 
    IN epassword VARCHAR(255)
)
BEGIN

    -- Insert from Purchase
    INSERT INTO audit_stock (
        product_id, voucher_id, store, date, product_name, scenario, incident, 
        voucherno, storename, stock, phystock, lastupdateddate
    )
    SELECT 
        pi.id AS product_id,
        p.id AS voucher_id,
        s.id AS store,
        p.date,
        pi.product_name, 
        'purchaseinvoice' AS scenario, 
        'purchase' AS incident, 
        p.chalan_no AS voucherno,
        s.name AS storename,
        (
            SELECT c.stock
            FROM stock_details c 
            WHERE c.product = pi.id 
              AND c.store = s.id 
              AND c.pid = p.id 
              AND c.type = 'purchase' 
              AND AES_DECRYPT(c.stock, epassword) = AES_DECRYPT(sd.quantity, epassword)
            LIMIT 1
        ) AS stock,
        (
            SELECT c.stock
            FROM phystock_details c 
            WHERE c.product = pi.id 
              AND c.store = s.id 
              AND c.pid = p.id 
              AND c.type = 'purchase' 
              AND AES_DECRYPT(c.stock, epassword) = AES_DECRYPT(sd.quantity, epassword)
            LIMIT 1
        ) AS phystock,
        p.lastupdateddate
    FROM `purchase` p
    INNER JOIN purchase_details sd ON sd.pid = p.id
    LEFT JOIN product_information pi ON sd.product = pi.id 
    LEFT JOIN store s ON s.id = sd.store
    WHERE p.already = 0;

    -- Insert from Sale
    INSERT INTO audit_stock (
        product_id, voucher_id, store, date, product_name, scenario, incident, 
        voucherno, storename, stock, phystock, lastupdateddate
    )
    SELECT 
        pi.id AS product_id,
        p.id AS voucher_id,
        s.id AS store,
        p.date,
        pi.product_name, 
        'saleinvoice' AS scenario,
        CASE 
            WHEN p.incidenttype = 1 THEN 'sale'
            WHEN p.incidenttype = 2 THEN 'wholesale'
            ELSE 'sale' 
        END AS incident, 
        p.sale_id AS voucherno,
        s.name AS storename,
        (
            SELECT c.stock
            FROM stock_details c 
            WHERE c.product = pi.id 
              AND c.store = s.id 
              AND c.pid = p.id 
              AND c.type = 'sales' 
              AND -AES_DECRYPT(c.stock, epassword) = AES_DECRYPT(sd.quantity, epassword)
            LIMIT 1
        ) AS stock,
        (
            SELECT c.stock
            FROM phystock_details c 
            WHERE c.product = pi.id 
              AND c.store = s.id 
              AND c.pid = p.id 
              AND c.type = 'sales' 
              AND -AES_DECRYPT(c.stock, epassword) = AES_DECRYPT(sd.quantity, epassword)
            LIMIT 1
        ) AS phystock,
        p.lastupdateddate
    FROM `sale` p
    INNER JOIN sale_details sd ON sd.pid = p.id
    LEFT JOIN product_information pi ON sd.product = pi.id 
    LEFT JOIN store s ON s.id = sd.store
    WHERE p.already = 0;

    -- Insert from GRN
    INSERT INTO audit_stock (
        product_id, voucher_id, store, date, product_name, scenario, incident, 
        voucherno, storename, stock, phystock, lastupdateddate
    )
    SELECT 
        pi.id AS product_id,
        p.id AS voucher_id,
        s.id AS store,
        p.date,
        pi.product_name, 
        'GRN' AS scenario, 
        p.type AS incident, 
        p.grn_id AS voucherno,
        s.name AS storename,
        NULL AS stock,
        (
            SELECT c.stock
            FROM phystock_details c 
            WHERE c.product = pi.id 
              AND c.store = s.id 
              AND c.pid = p.id 
              AND c.type = 'grn_stock' 
              AND AES_DECRYPT(c.stock, epassword) = AES_DECRYPT(sd.stock, epassword)
            LIMIT 1
        ) AS phystock,
        p.lastupdateddate
    FROM `grn_stock` p
    INNER JOIN phystock_details sd ON sd.pid = p.id
    LEFT JOIN product_information pi ON sd.product = pi.id 
    LEFT JOIN store s ON s.id = sd.store
    WHERE sd.type = 'grn_stock'
      AND p.already = 0;

    -- Insert from GDN
    INSERT INTO audit_stock (
        product_id, voucher_id, store, date, product_name, scenario, incident, 
        voucherno, storename, stock, phystock, lastupdateddate
    )
    SELECT 
        pi.id AS product_id,
        p.id AS voucher_id,
        s.id AS store,
        p.date,
        pi.product_name, 
        'GDN' AS scenario, 
        p.type AS incident, 
        p.gdn_id AS voucherno,
        s.name AS storename,
        NULL AS stock,
        (
            SELECT c.stock
            FROM phystock_details c 
            WHERE c.product = pi.id 
              AND c.store = s.id 
              AND c.pid = p.id 
              AND c.type = 'gdn_stock' 
              AND AES_DECRYPT(c.stock, epassword) = AES_DECRYPT(sd.stock, epassword)
            LIMIT 1
        ) AS phystock,
        p.lastupdateddate
    FROM `gdn_stock` p
    INNER JOIN phystock_details sd ON sd.pid = p.id
    LEFT JOIN product_information pi ON sd.product = pi.id 
    LEFT JOIN store s ON s.id = sd.store
    WHERE sd.type = 'gdn_stock'
      AND p.already = 0;

    -- Update audit flags
    UPDATE purchase     SET already = 1 WHERE already = 0;
    UPDATE sale         SET already = 1 WHERE already = 0;
    UPDATE grn_stock    SET already = 1 WHERE already = 0;
    UPDATE gdn_stock    SET already = 1 WHERE already = 0;

END$$

DELIMITER ;
