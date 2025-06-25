DELIMITER $$

CREATE PROCEDURE `sp_get_stock_audit` ( 
    IN epassword      VARCHAR(255),
    IN in_product_id  INT,
    IN in_from_date   DATE,
    IN in_to_date     DATE,
    IN in_scenario    VARCHAR(255),
    IN in_incident    VARCHAR(255),
    IN in_store       INT)
BEGIN
    SELECT *
    FROM (
        -- audit_stock
        SELECT 
            product_id,
            voucher_id,
            store,
            date,
            product_name, 
            scenario, 
            incident, 
            AES_DECRYPT(voucherno, epassword) AS voucherno,
            storename,
            AES_DECRYPT(stock, epassword) AS stock,
            AES_DECRYPT(phystock, epassword) AS phystock,
            lastupdateddate
        FROM `audit_stock` 
        

        UNION

        -- Stock Adjustment
        SELECT 
            CASE 
                WHEN p.stocktype = 'actualstock' THEN pi.id
                WHEN p.stocktype = 'both' THEN pi1.id
                ELSE pi2.id 
            END AS product_id,
            p.id AS voucher_id,
            CASE 
                WHEN p.stocktype = 'actualstock' THEN s.id
                WHEN p.stocktype = 'both' THEN s1.id
                ELSE s2.id 
            END AS store,
            p.date,
            CASE 
                WHEN p.stocktype = 'actualstock' THEN pi.product_name
                WHEN p.stocktype = 'both' THEN pi1.product_name
                ELSE pi2.product_name 
            END AS product_name,
            'stock' AS scenario, 
            p.type AS incident, 
            p.id AS voucherno,
            CASE 
                WHEN p.stocktype = 'actualstock' THEN s.name
                WHEN p.stocktype = 'both' THEN s1.name
                ELSE s2.name 
            END AS storename,
            CASE 
                WHEN p.stocktype = 'actualstock' THEN AES_DECRYPT(sd.stock, epassword)
                WHEN p.stocktype = 'both' THEN AES_DECRYPT(sd1.stock, epassword)
            END AS stock,
            CASE 
                WHEN p.stocktype = 'actualstock' AND s.auto_grn = 0 and  AES_DECRYPT(sd.stock, epassword)>=0 and (p.type='storetransfer' or p.type='stockdisposal') THEN AES_DECRYPT(sd.stock, epassword)
                 WHEN p.stocktype = 'actualstock' AND s.auto_gdn = 0 and  AES_DECRYPT(sd.stock, epassword)<0 and (p.type='storetransfer' or p.type='stockdisposal') THEN AES_DECRYPT(sd.stock, epassword)
                WHEN p.stocktype = 'physicalstock' THEN AES_DECRYPT(pd.stock, epassword)
                WHEN p.stocktype = 'both' THEN AES_DECRYPT(sd1.stock, epassword)
            END AS phystock,
            CAST(p.date AS DATETIME) AS lastupdateddate
        FROM `adj_stock` p
        LEFT JOIN stock_details sd ON sd.pid = p.id AND p.stocktype = 'actualstock' AND sd.type = 'adj_stock'
        LEFT JOIN stock_details sd1 ON sd1.pid = p.id AND p.stocktype = 'both' AND sd1.type = 'adj_stock'
        LEFT JOIN phystock_details pd ON pd.pid = p.id AND p.stocktype = 'physicalstock' AND pd.type = 'adj_stock'
        LEFT JOIN product_information pi ON sd.product = pi.id 
        LEFT JOIN product_information pi1 ON sd1.product = pi1.id 
        LEFT JOIN product_information pi2 ON pd.product = pi2.id 
        LEFT JOIN store s ON s.id = sd.store
        LEFT JOIN store s1 ON s1.id = sd1.store
        LEFT JOIN store s2 ON s2.id = pd.store

         UNION

        -- Opening Stock
        SELECT 
            sd.product AS product_id,
            0 AS voucher_id,
             s.id AS store,
            sd.date,
            pi.product_name,
            '' AS scenario,
            'opening_stock' AS incident,
            0 AS voucherno,
            s.name AS storename,
            AES_DECRYPT(sd.stock, epassword) AS stock,
            AES_DECRYPT(sd.stock, epassword) AS phystock,
            CAST(sd.date AS DATETIME) AS lastupdateddate
        FROM stock_details sd
        LEFT JOIN product_information pi ON sd.product = pi.id 
        LEFT JOIN store s ON s.id = sd.store
        WHERE sd.pid = 0 AND sd.product != 0
    ) AS audit
    WHERE audit.product_id = in_product_id
    AND audit.date BETWEEN in_from_date AND in_to_date
    AND (in_scenario IS NULL OR in_scenario = '' OR audit.scenario = in_scenario)
    AND (in_incident IS NULL OR in_incident = '' OR audit.incident = in_incident)
    AND (in_store IS NULL OR in_store = '' OR audit.store = in_store)
    ORDER BY audit.date , audit.lastupdateddate ,audit.scenario,audit.voucherno,audit.store ;
END$$

DELIMITER ;
