-- Mission 4 : ce fichier est un carnet de conception, pas un schéma prêt à importer.
-- Cible de l'exercice : MySQL dans WampServer sous Windows 11.
-- Dans phpMyAdmin, sélectionne ta base de test avant tout SQL :
-- atelier_mosaique, ou le nom choisi ensemble en mission 4.
-- TODO : choisir les colonnes, types et contraintes avant d'écrire le SQL.
-- projects : comment identifier un projet et mémoriser son nom ?
-- images : comment rattacher plusieurs images au même projet ?
-- Quelles informations distinguent le nom d'origine du nom stocké sur disque ?
-- Comment conserver dimensions, densité, espacement, arrondi, fond,
-- transparence, marge et seed lorsque tu arriveras en mission 7 ?
-- Ne placer ici ni identifiants de connexion, ni données personnelles.

CREATE TABLE projects (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Une ligne par image ; le fichier lui-même reste dans storage/projects/.
CREATE TABLE images (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    storage_name VARCHAR(40) NOT NULL UNIQUE,
    mime_type VARCHAR(50) NOT NULL,
    size_bytes INT UNSIGNED NOT NULL,
    width INT UNSIGNED NOT NULL,
    height INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_images_project
        FOREIGN KEY (project_id) REFERENCES projects(id)
        ON DELETE CASCADE,
    INDEX idx_images_project_id (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
