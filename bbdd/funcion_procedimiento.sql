DELIMITER $$

DROP FUNCTION IF EXISTS fn_dias_abierta $$

CREATE FUNCTION fn_dias_abierta(p_id_incidencia INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE dias INT;

    SELECT DATEDIFF(NOW(), fecha_creacion)
    INTO dias
    FROM incidencias
    WHERE id_incidencia = p_id_incidencia;

    RETURN dias;
END $$

DELIMITER ;

--Con esto podemos ver cuantos dias lleva abierta una incidencia--

SELECT fn_dias_abierta(1);


--PROCEDIMIENTO--

DELIMITER $$

CREATE PROCEDURE sp_cerrar_incidencia(
    IN p_id_incidencia INT,
    IN p_id_admin INT
)
BEGIN

    UPDATE incidencias
    SET id_estado = 4
    WHERE id_incidencia = p_id_incidencia;

    INSERT INTO comentarios
    (
        comentario,
        id_usuario,
        id_incidencia
    )
    VALUES
    (
        'Incidencia cerrada mediante procedimiento almacenado.',
        p_id_admin,
        p_id_incidencia
    );

END $$

DELIMITER ;

--   Cuando se ejecuta este procedimiento la incidencia queda cerrada automaticamente y deja--
-- escrito un comentario que aporta trazabilidad--
CALL sp_cerrar_incidencia(12,1);

--TRIGGER--

--Primero creamos una nueva tabla para auditar los cambios de estado--
CREATE TABLE auditoria_estados
(
    id_auditoria INT AUTO_INCREMENT PRIMARY KEY,

    id_incidencia INT,

    estado_anterior INT,

    estado_nuevo INT,

    fecha_cambio TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP
);

--Luego creamos el trigger que detecta automaticamente cada cambio de estado--

DELIMITER $$

CREATE TRIGGER trg_cambio_estado
AFTER UPDATE
ON incidencias
FOR EACH ROW
BEGIN

    IF OLD.id_estado <> NEW.id_estado THEN

        INSERT INTO auditoria_estados
        (
            id_incidencia,
            estado_anterior,
            estado_nuevo
        )
        VALUES
        (
            NEW.id_incidencia,
            OLD.id_estado,
            NEW.id_estado
        );

    END IF;

END $$

DELIMITER ;


