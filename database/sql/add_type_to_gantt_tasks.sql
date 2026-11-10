ALTER TABLE `gantt_tasks`
    ADD COLUMN `type` VARCHAR(20) NOT NULL DEFAULT 'task' AFTER `duration`;
