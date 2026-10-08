-- Schéma initial de la base locale atelier_mosaique.
-- Les fichiers image sont stockés dans storage/projects/<project_id>/.

CREATE TABLE projects (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    archived_at TIMESTAMP NULL DEFAULT NULL,
    mosaic_width SMALLINT UNSIGNED NOT NULL DEFAULT 1200,
    mosaic_height SMALLINT UNSIGNED NOT NULL DEFAULT 900,
    mosaic_mode ENUM('dense', 'normal', 'aere') NOT NULL DEFAULT 'normal',
    mosaic_gap TINYINT UNSIGNED NOT NULL DEFAULT 8,
    mosaic_radius TINYINT UNSIGNED NOT NULL DEFAULT 0,
    mosaic_bg CHAR(7) NOT NULL DEFAULT '#12121a',
    mosaic_bg_transparent TINYINT(1) NOT NULL DEFAULT 0,
    mosaic_margin SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    mosaic_seed INT UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_projects_archived_at (archived_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE images (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    storage_name CHAR(36) NOT NULL,
    mime_type ENUM('image/jpeg', 'image/png') NOT NULL,
    size_bytes INT UNSIGNED NOT NULL,
    width INT UNSIGNED NOT NULL,
    height INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_images_storage_name (project_id, storage_name),
    KEY idx_images_project_id (project_id),
    CONSTRAINT fk_images_project
        FOREIGN KEY (project_id) REFERENCES projects (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
