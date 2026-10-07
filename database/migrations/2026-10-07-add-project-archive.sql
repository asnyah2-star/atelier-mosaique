-- À exécuter une seule fois sur la base atelier_mosaique existante.
-- Rend l'archivage réversible sans supprimer projets ni images.
ALTER TABLE projects
    ADD COLUMN archived_at TIMESTAMP NULL DEFAULT NULL;
