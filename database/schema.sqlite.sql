PRAGMA foreign_keys = ON;

CREATE TABLE projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL CHECK(length(name) <= 120),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    archived_at TEXT DEFAULT NULL,
    mosaic_width INTEGER NOT NULL DEFAULT 1200,
    mosaic_height INTEGER NOT NULL DEFAULT 900,
    mosaic_mode TEXT NOT NULL DEFAULT 'normal' CHECK(mosaic_mode IN ('dense', 'normal', 'aere')),
    mosaic_gap INTEGER NOT NULL DEFAULT 8,
    mosaic_radius INTEGER NOT NULL DEFAULT 0,
    mosaic_bg TEXT NOT NULL DEFAULT '#12121a',
    mosaic_bg_transparent INTEGER NOT NULL DEFAULT 0 CHECK(mosaic_bg_transparent IN (0, 1)),
    mosaic_margin INTEGER NOT NULL DEFAULT 0,
    mosaic_seed INTEGER DEFAULT NULL
);
CREATE INDEX idx_projects_archived_at ON projects(archived_at);

CREATE TABLE images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    project_id INTEGER NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
    original_name TEXT NOT NULL,
    storage_name TEXT NOT NULL,
    mime_type TEXT NOT NULL CHECK(mime_type IN ('image/jpeg', 'image/png')),
    size_bytes INTEGER NOT NULL,
    width INTEGER NOT NULL,
    height INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(project_id, storage_name)
);
CREATE INDEX idx_images_project_id ON images(project_id);
