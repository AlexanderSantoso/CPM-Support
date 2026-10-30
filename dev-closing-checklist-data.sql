-- Data dev untuk Closing Document Checklist (database lokal cpm2).
-- Hanya mengisi atau mengubah baris pada tabel yang sudah ada, tanpa kolom atau tabel baru.
-- Aman dijalankan berulang kali (baris baru memakai id tetap dengan ON DUPLICATE KEY UPDATE).

-- 1) checklist_item berupa daftar id item yang wajib. Checklist 1 dipilihkan 8 item supaya demo punya 8 baris.
UPDATE administration_closing_document_checklists SET checklist_item = JSON_ARRAY(1, 2, 3, 4, 5, 6, 7, 8) WHERE id = 1;
UPDATE administration_closing_document_checklists SET checklist_item = JSON_ARRAY(2, 4) WHERE id = 2;

-- 2) Item master tambahan (nama diambil dari template acuan).
INSERT INTO administration_closing_document_checklist_items (id, uuid, name) VALUES
  (5, UUID(), 'Master Schedule'),
  (6, UUID(), 'Material Approval'),
  (7, UUID(), 'Final Account'),
  (8, UUID(), 'Defect Checklist')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 3) Aktivitas checklist 1 yang sudah ada (id 1 sampai 4): status dan tanggal dibedakan.
--    Aktivitas 3 diubah dari Reviewed (2) menjadi Revision Required (5) supaya ada contoh warna merah.
UPDATE administration_closing_document_checklist_activities SET due_date = '2026-09-10' WHERE id = 1;
UPDATE administration_closing_document_checklist_activities SET due_date = '2026-09-20' WHERE id = 2;
UPDATE administration_closing_document_checklist_activities SET due_date = '2026-10-05', log_status_id = 5 WHERE id = 3;
UPDATE administration_closing_document_checklist_activities SET due_date = '2026-10-15' WHERE id = 4;

-- 4) Aktivitas baru untuk checklist 1 (id 6 sampai 9), satu per item tambahan.
--    Aktivitas terakhir (id 9) berstatus Approved supaya tetap cocok dengan status dokumen (log_status_id = 3).
INSERT INTO administration_closing_document_checklist_activities
  (id, adm_closing_document_checklist_id, log_status_id, step_order, assigned_to, cc, due_date, remarks, created_by, created_at)
VALUES
  (6, 1, 4, 4, NULL, NULL, '2026-10-20', NULL, 5, '2026-09-02 15:01:00'),
  (7, 1, 7, 5, NULL, NULL, '2026-10-25', NULL, 5, '2026-09-02 15:02:00'),
  (8, 1, 9, 6, NULL, NULL, '2026-11-05', NULL, 5, '2026-09-02 15:03:00'),
  (9, 1, 3, 7, NULL, NULL, '2026-11-15', NULL, 5, '2026-09-02 15:04:00')
ON DUPLICATE KEY UPDATE log_status_id = VALUES(log_status_id), due_date = VALUES(due_date), created_at = VALUES(created_at);

-- 5) Checklist 2 hanya punya satu aktivitas.
UPDATE administration_closing_document_checklist_activities SET due_date = '2026-10-31' WHERE id = 5;

SELECT i.id AS item_id, i.name AS item, s.status, a.due_date
FROM administration_closing_document_checklist_items i
LEFT JOIN (
  SELECT a.*, ROW_NUMBER() OVER (ORDER BY a.created_at, a.id) AS n
  FROM administration_closing_document_checklist_activities a
  WHERE a.adm_closing_document_checklist_id = 1
) a ON a.n = i.id
LEFT JOIN log_statuses s ON s.id = a.log_status_id
ORDER BY i.id;
